@include('components.head')
@include('components.navbar')

<div class="container main-body">
    <!-- Main Content -->
    <main>
        <section class="welcome-section">
            <div class="welcome-header">
                @auth
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/pisang.png') }}"
                    alt="profile-image" class="welcome-avatar">
                @else
                <img src="{{ asset('images/pisang.png') }}" alt="profile-image" class="welcome-avatar">
                @endauth

                <div class="welcome-content">
                    <h1 class="welcome-title">
                        @auth
                        Welcome {{ Auth::user()->name }} to the CalorieTrack forum!
                        @else
                        Welcome to the CalorieTrack forum!
                        @endauth
                    </h1>
                    <p class="welcome-description">
                        This is a space to ask questions, share experiences, and discuss all things related to calories,
                        nutrition, healthy diets, and everyday wellness.
                    </p>
                </div>
            </div>
        </section>

        <!-- Topic Selector Section -->
        <div class="topic-selector-section">
            <div class="topic-selector-container">
                <form method="GET" action="{{ route('forum.filterByTime') }}" id="timeRangeForm">
                    <select class="topic-selector" id="topicSelector" name="timeRange"
                        onchange="document.getElementById('timeRangeForm').submit()">
                        <option value="">Select a discussion time range</option>
                        <option value="today" {{ $timeRange=='today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $timeRange=='week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $timeRange=='month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $timeRange=='year' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $timeRange=='all' ? 'selected' : '' }}>All Time</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Forum Posts -->
        <div class="forum-posts">
            @foreach($posts as $post)
            <div class="post" data-id="{{ $post->id }}">
                <!-- Post Header -->
                <div class="post-header">
                    @php
                    $userAvatar = isset($post->user->avatar_url) ? $post->user->avatar_url :
                    asset('images/default-avatar.png');
                    $userName = isset($post->user->name) ? $post->user->name : 'Deleted User';
                    @endphp

                    <img class="post-avatar" src="{{ $userAvatar }}" alt="{{ $userName }}">

                    <div class="post-meta-info">
                        <div class="post-author-name">{{ $userName }}</div>
                        <div class="post-date-category">
                            <span>Asked {{ $post->created_at->format('M j, Y') }}</span>
                            <span class="post-category-tag">{{ $post->category }}</span>
                        </div>
                    </div>
                </div>

                <!-- Post Content -->
                <div class="post-content-section">
                    <h3 class="post-title">{{ $post->title }}</h3>
                    <p class="post-content">{{ Str::limit($post->content, 150) }}</p>
                </div>

                <!-- Post Footer -->
                <div class="post-footer">
                    <div class="post-stats">
                        <div class="answers-stat">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            <span>{{ $post->answers_count }} Answers</span>
                        </div>

                        <div class="views-stat">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <span id="views-count-{{ $post->id }}">{{ number_format($post->views_count ?? 0) }}
                                Views</span>
                        </div>

                        <div class="likes-stat">
                            <svg class="like-button" style="color: {{ $post->isLikedByCurrentUser() ? '#EC4899' : '#9CA3AF' }}; 
                                        fill: {{ $post->isLikedByCurrentUser() ? '#EC4899' : '#9CA3AF' }}; 
                                        cursor: pointer; width: 20px; height: 20px;" data-post-id="{{ $post->id }}"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                            <span class="likes-count" data-post-id="{{ $post->id }}">{{ $post->likes_count }}
                                Likes</span>
                        </div>
                    </div>

                    <a href="javascript:void(0)" onclick="goToAnswer({{ $post->id }})" class="btn-answer">
                        Answer
                    </a>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $posts->links() }}
            </div>
        </div>
    </main>

    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Ask Question Button -->
        <div class="ask-question-section">
            <a onclick="openModal()" class="ask-question-btn">+ Ask Question</a>
        </div>

        <!-- Stats Card -->
        <div class="stats-card">
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-label">Questions</div>
                    <span class="stat-number">{{ number_format($stats['total_questions']) }}</span>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Answers</div>
                    <span class="stat-number">{{ number_format($stats['total_answers']) }}</span>
                </div>
            </div>
        </div>

        <h3 class="activity-title">
            <img src="{{ asset('images/activity-image.png') }}" alt="activity-image">
            Recent Activity
        </h3>

        <!-- Recent Activity -->
        <div class="recent-activity">
            @if($recentActivity->count() > 0)
            @foreach($recentActivity as $activity)
            <div class="activity-item">
                <div class="activity-avatar">
                    <img src="{{ $activity->avatar ?? asset('images/pisang.png') }}" alt="profile-image"
                        class="activity-img-avatar">
                </div>
                <div class="activity-content">
                    <div class="activity-text">
                        <span class="highlight">{{ $activity->name ?? 'Unknown User' }}</span> started following you
                    </div>
                    <div class="activity-time">{{ isset($activity->created_at) ?
                        \Carbon\Carbon::parse($activity->created_at)->format('F j \\a\\t g:i A') : 'Unknown time' }}
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="activity-item">
                <div class="activity-content">
                    <div class="activity-text">No recent activity</div>
                </div>
            </div>
            @endif
        </div>

        <h3 class="users-title">
            <span>👥</span>
            Active Users
        </h3>

        <!-- Active Users -->
        <div class="online-users">
            <div class="active-users-display">
                <div class="user-avatars-row">
                    @foreach($activeUsers->take(5) as $user)
                    <div class="user-avatar-large">
                        <img src="{{ $user->avatar_url ?? asset('images/pisang.png') }}" alt="profile-image"
                            class="activity-user-avatar" title="{{ $user->name ?? 'Unknown User' }}">
                    </div>
                    @endforeach
                </div>
                @if($activeUsers->count() > 5)
                <div class="others-count-large">+{{ $activeUsers->count() - 5 }} others</div>
                @endif
            </div>
        </div>
    </aside>
</div>

<div id="askQuestionModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-character">
                <div class="character-avatar">
                    <img src="{{ asset('images/avatar-question.png') }}" alt="">
                </div>
            </div>

            <h2 class="modal-title">Ask a Question</h2>

            <form id="questionForm" class="question-form">
                @csrf
                <div class="form-group">
                    <input type="text" id="questionTitle" class="form-control" placeholder="Question Title" required>
                </div>

                <div class="form-group">
                    <select id="questionCategory" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="Weight Loss">Weight Loss</option>
                        <option value="Weight Gain">Weight Gain</option>
                        <option value="Maintain Weight">Maintain Weight</option>
                        <option value="Nutrition">Nutrition</option>
                        <option value="Exercise">Exercise</option>
                        <option value="General Health">General Health</option>
                    </select>
                </div>

                <div class="form-group">
                    <textarea id="questionContent" class="form-control" placeholder="Enter your question details here!"
                        rows="4" required></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="back-button" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="submit-button">Post Question</button>
                </div>

                <!-- Error message container -->
                <div id="formError" class="error-message" style="color: red; display: none;"></div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('askQuestionModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('askQuestionModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('questionForm').reset();
        document.getElementById('formError').style.display = 'none';
    }

    // Close modal when clicking overlay
    document.getElementById('askQuestionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Handle form submission dengan AJAX
    document.getElementById('questionForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const title = document.getElementById('questionTitle').value.trim();
        const category = document.getElementById('questionCategory').value;
        const content = document.getElementById('questionContent').value.trim();
        const errorElement = document.getElementById('formError');
        const submitButton = this.querySelector('button[type="submit"]');

        // Reset error
        errorElement.style.display = 'none';
        errorElement.textContent = '';

        // Validation
        if (!title) {
            showError('Please enter a question title');
            return;
        }

        if (!category) {
            showError('Please select a category');
            return;
        }

        if (!content) {
            showError('Please enter question content');
            return;
        }

        // Disable button dan show loading
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Posting...';

        try {
            const formData = new FormData();
            formData.append('title', title);
            formData.append('category', category);
            formData.append('content', content);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('{{ route("forum.store.ajax") }}', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Tutup modal
                closeModal();

                // Tampilkan pesan sukses
                alert('Question posted successfully!');

                // Refresh halaman untuk melihat post baru
                window.location.reload();
            } else {
                // Tampilkan error dari server
                let errorMsg = 'Failed to post question';

                if (data.errors) {
                    // Jika ada multiple errors
                    errorMsg = Object.values(data.errors).flat().join(', ');
                } else if (data.message) {
                    errorMsg = data.message;
                }

                showError(errorMsg);
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Network error. Please check your connection and try again.');
        } finally {
            // Enable button kembali
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });

    function showError(message) {
        const errorElement = document.getElementById('formError');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Function to go to answer page with views increment
    async function goToAnswer(postId) {
        try {
            const response = await fetch(`/forum/posts/${postId}/increment-views`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Update the views count on the page
                const viewsElement = document.getElementById(`views-count-${postId}`);
                if (viewsElement) {
                    viewsElement.textContent = `${data.views_count} Views`;
                }
            }
        } catch (error) {
            console.error('Error incrementing views:', error);
        } finally {
            // Navigate to answer page regardless of increment success
            window.location.href = `/forum/answer/${postId}`;
        }
    }

    // Handle like functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to like buttons
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const postId = this.getAttribute('data-post-id');
                toggleLike(postId, this);
            });
        });
    });

    async function toggleLike(postId, buttonElement) {
        console.log('Toggle like called for post:', postId);
        
        // Prevent multiple clicks
        if (buttonElement.classList.contains('processing')) {
            return;
        }
        
        buttonElement.classList.add('processing');
        
        try {
            const response = await fetch('/forum/toggle-like', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    type: "post",
                    id: parseInt(postId)
                })
            });

            console.log('Response status:', response.status);
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                // Update the like count
                const likesCountElement = document.querySelector(`.likes-count[data-post-id="${postId}"]`);
                if (likesCountElement) {
                    likesCountElement.textContent = `${data.likes_count} Likes`;
                }
                
                // Update the button appearance
                if (data.liked) {
                    buttonElement.style.color = '#EC4899';
                    buttonElement.style.fill = '#EC4899';
                    console.log('Liked - changing to pink');
                } else {
                    buttonElement.style.color = '#9CA3AF';
                    buttonElement.style.fill = '#9CA3AF';
                    console.log('Unliked - changing to gray');
                }
            } else {
                console.error('Failed to toggle like:', data.message);
                alert('Failed to update like. Please try again.');
            }
        } catch (error) {
            console.error('Error toggling like:', error);
            alert('Network error. Please try again.');
        } finally {
            buttonElement.classList.remove('processing');
        }
    }
</script>