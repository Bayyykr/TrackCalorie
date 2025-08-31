<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function forum(Request $request)
    {
        $timeRange = $request->get('timeRange', '');

        // Get forum posts dengan safe access
        $postsQuery = ForumPost::with([
            'user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }
        ])
            ->withCount(['answers', 'likes'])
            ->orderBy('created_at', 'desc');

        if ($timeRange && $timeRange !== 'all') {
            $postsQuery->byTimeRange($timeRange);
        }

        $posts = $postsQuery->paginate(3);

        $posts->getCollection()->transform(function ($post) {
            // Pastikan user object ada
            if (!$post->user) {
                $post->user = (object) [
                    'id' => 0,
                    'name' => 'Deleted User',
                    'avatar' => 'images/default-avatar.png'
                ];
            }

            // Pastikan avatar_url ada
            $post->user->avatar_url = isset($post->user->avatar) && $post->user->avatar
                ? asset('storage/' . $post->user->avatar)
                : asset('images/default-avatar.png');

            return $post;
        });

        // Recent activity dengan safe access
        $recentActivity = collect();
        if (Auth::check()) {
            $recentActivity = DB::table('user_follows')
                ->join('users', 'user_follows.follower_id', '=', 'users.id')
                ->where('user_follows.following_id', Auth::id())
                ->select('users.id', 'users.name', 'users.avatar', 'user_follows.created_at')
                ->orderBy('user_follows.created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'name' => $item->name ?? 'Unknown User',
                        'avatar' => $item->avatar ? asset('storage/' . $item->avatar) : asset('images/default-avatar.png'),
                        'created_at' => $item->created_at
                    ];
                });
        }

        // Active users dengan safe access
        $activeUsers = User::select('id', 'name', 'avatar')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('forum_posts')
                    ->whereColumn('forum_posts.user_id', 'users.id')
                    ->where('forum_posts.created_at', '>=', now()->subDays(7));
            })
            ->orWhereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('forum_answers')
                    ->whereColumn('forum_answers.user_id', 'users.id')
                    ->where('forum_answers.created_at', '>=', now()->subDays(7));
            })
            ->distinct()
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return (object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png')
                ];
            });

        $stats = [
            'total_questions' => ForumPost::count(),
            'total_answers' => ForumAnswer::count(),
        ];

        return view('auth.forum', compact('posts', 'recentActivity', 'activeUsers', 'stats', 'timeRange'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        $post = ForumPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
        ]);

        // Redirect kembali ke forum utama setelah membuat post
        return redirect()->route('forum.forum')
            ->with('success', 'Question posted successfully!');
    }

    // Method untuk menangani klik tombol Answer dan increment views
    public function answer($post)
    {
        $post = ForumPost::with(['user', 'answers.user'])
            ->findOrFail($post);

        // Increment views count
        $post->incrementViews();

        // Transform post user untuk safe access
        if (!$post->user) {
            $post->user = (object) [
                'id' => 0,
                'name' => 'Deleted User',
                'avatar' => 'images/default-avatar.png'
            ];
        }

        $post->user->avatar_url = isset($post->user->avatar) && $post->user->avatar
            ? asset('storage/' . $post->user->avatar)
            : asset('images/default-avatar.png');

        // Transform answers untuk safe access
        $post->answers->transform(function ($answer) {
            if (!$answer->user) {
                $answer->user = (object) [
                    'id' => 0,
                    'name' => 'Deleted User',
                    'avatar' => 'images/default-avatar.png'
                ];
            }

            $answer->user->avatar_url = isset($answer->user->avatar) && $answer->user->avatar
                ? asset('storage/' . $answer->user->avatar)
                : asset('images/default-avatar.png');

            return $answer;
        });

        return view('answerquest', compact('post'));
    }

    public function storeAnswer(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:forum_posts,id',
        ]);

        $answer = ForumAnswer::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Update post answers count
        ForumPost::where('id', $request->post_id)->increment('answers_count');

        // Redirect ke halaman answerquest
        return redirect()->route('forum.answer', ['post' => $request->post_id])
            ->with('success', 'Answer posted successfully!');
    }

    public function toggleLike(Request $request)
    {
        try {
            \Log::info('Toggle like called', [
                'request_data' => $request->all(),
                'session_id' => session()->getId()
            ]);

            $request->validate([
                'type' => 'required|string|in:post,answer',
                'id' => 'required|integer'
            ]);

            $type = $request->input('type');
            $id = $request->input('id');

            if ($type === 'post') {
                $model = ForumPost::findOrFail($id);
            } else {
                $model = ForumAnswer::findOrFail($id);
            }

            // Untuk guest, gunakan session ID
            $session_id = session()->getId();

            // Check if already liked
            $existingLike = $model->likes()->where('session_id', $session_id)->first();

            if ($existingLike) {
                // Unlike
                $existingLike->delete();
                $model->decrement('likes_count');
                $liked = false;
                \Log::info('Post unliked', ['post_id' => $id, 'session_id' => $session_id]);
            } else {
                // Like
                $model->likes()->create([
                    'session_id' => $session_id,
                    'likeable_id' => $model->id,
                    'likeable_type' => get_class($model)
                ]);
                $model->increment('likes_count');
                $liked = true;
                \Log::info('Post liked', ['post_id' => $id, 'session_id' => $session_id]);
            }

            // Get fresh data
            $model = $model->fresh();

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $model->likes_count
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Like validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Like model not found', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Like toggle error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle like'
            ], 500);
        }
    }

    public function follow(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Cannot follow yourself'], 400);
        }

        if (!$currentUser->isFollowing($user)) {
            $currentUser->following()->attach($user->id);
            return response()->json([
                'success' => true,
                'following' => true,
                'message' => 'You are now following ' . $user->name
            ]);
        }

        return response()->json(['error' => 'Already following this user'], 400);
    }

    public function unfollow(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($currentUser->isFollowing($user)) {
            $currentUser->following()->detach($user->id);
            return response()->json([
                'success' => true,
                'following' => false,
                'message' => 'You unfollowed ' . $user->name
            ]);
        }

        return response()->json(['error' => 'Not following this user'], 400);
    }

    public function filterByTime(Request $request)
    {
        $timeRange = $request->get('timeRange', 'all');
        return $this->forum($request);
    }

    public function storeAjax(Request $request)
    {
        \Log::info('Store AJAX called', ['data' => $request->all()]);

        try {
            // Validasi input
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|max:100',
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            // Buat forum post baru
            $post = ForumPost::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                // Kolom berikut mungkin tidak perlu diisi karena sudah ada default value
                'views_count' => 0,
                'likes_count' => 0,
                'answers_count' => 0
            ]);

            \Log::info('Post created successfully', ['post_id' => $post->id]);

            return response()->json([
                'success' => true,
                'message' => 'Question posted successfully!',
                'post' => $post
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Forum post error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to post question. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function incrementViews(ForumPost $post)
    {
        try {
            // Use the model's incrementViews method instead of Eloquent's increment
            $post->incrementViews();

            return response()->json([
                'success' => true,
                'views_count' => $post->fresh()->views_count
            ]);
        } catch (\Exception $e) {
            \Log::error('Error incrementing views: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to increment views'
            ], 500);
        }
    }
}
