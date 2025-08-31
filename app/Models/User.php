<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function forumPosts()
{
    return $this->hasMany(ForumPost::class);
}

public function forumAnswers()
{
    return $this->hasMany(ForumAnswer::class);
}

public function forumLikes()
{
    return $this->hasMany(ForumLike::class);
}

// Add these methods for following functionality (if not already present)
public function following()
{
    return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id')
                ->withTimestamps();
}

public function followers()
{
    return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id')
                ->withTimestamps();
}

public function isFollowing(User $user)
{
    return $this->following()->where('following_id', $user->id)->exists();
}

public function isFollowedBy(User $user)
{
    return $this->followers()->where('follower_id', $user->id)->exists();
}

    // Accessor for safe avatar access
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return asset('images/default-avatar.png');
    }
}
