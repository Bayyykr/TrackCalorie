<?php
// database/seeders/ForumSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ForumPost;
use App\Models\ForumAnswer;
use App\Models\ForumLike;
use App\Models\UserFollow;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ForumSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel terlebih dahulu untuk menghindari konflik
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserFollow::truncate();
        ForumLike::truncate();
        ForumAnswer::truncate();
        ForumPost::truncate();

        // Hapus user yang mungkin sudah dibuat oleh seeder sebelumnya
        User::where('email', 'like', '%@example.com')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create users
        $users = User::factory()->count(20)->create([
            'password' => Hash::make('password123'),
        ]);

        // Create specific test user
        $testUser = User::create([
            'name' => 'Anang Ma\'ruf',
            'email' => 'anang@example.com',
            'password' => Hash::make('password123'),
            'avatar' => 'images/pisang.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $users->push($testUser);

        // Categories for posts
        $categories = [
            'Weight Loss',
            'Weight Gain',
            'Maintain Weight',
            'Nutrition',
            'Exercise',
            'Recipes',
            'Mental Health',
            'General'
        ];

        // Create forum posts
        $posts = [];
        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 90));

            $posts[] = ForumPost::create([
                'user_id' => $users->random()->id,
                'title' => $this->generatePostTitle(),
                'content' => $this->generatePostContent(),
                'category' => $categories[array_rand($categories)],
                'views_count' => rand(50, 2554),
                'likes_count' => rand(5, 1880),
                'answers_count' => rand(3, 993),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        // Create answers for posts
        foreach ($posts as $post) {
            $answerCount = rand(3, 20);
            for ($i = 0; $i < $answerCount; $i++) {
                $answerDate = $post->created_at->addDays(rand(0, 30));

                ForumAnswer::create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                    'content' => $this->generateAnswerContent(),
                    'likes_count' => rand(0, 50),
                    'is_best_answer' => $i === 0 ? rand(0, 1) : false,
                    'created_at' => $answerDate,
                    'updated_at' => $answerDate,
                ]);
            }
        }

        // Create likes for posts and answers
        foreach ($users as $user) {
            // Like some posts
            $postsToLike = collect($posts)->random(rand(5, 15));
            foreach ($postsToLike as $post) {
                ForumLike::firstOrCreate([
                    'user_id' => $user->id,
                    'likeable_id' => $post->id,
                    'likeable_type' => ForumPost::class,
                ], [
                    'created_at' => $post->created_at->addDays(rand(1, 10)),
                    'updated_at' => now(),
                ]);
            }

            // Like some answers
            $answers = ForumAnswer::inRandomOrder()->limit(rand(5, 20))->get();
            foreach ($answers as $answer) {
                ForumLike::firstOrCreate([
                    'user_id' => $user->id,
                    'likeable_id' => $answer->id,
                    'likeable_type' => ForumAnswer::class,
                ], [
                    'created_at' => $answer->created_at->addDays(rand(1, 5)),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create follow relationships using firstOrCreate to avoid duplicates
        foreach ($users as $user) {
            $usersToFollow = $users->where('id', '!=', $user->id)->random(rand(3, 8));

            foreach ($usersToFollow as $userToFollow) {
                UserFollow::firstOrCreate([
                    'follower_id' => $user->id,
                    'following_id' => $userToFollow->id,
                ], [
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now(),
                ]);
            }
        }

        // Ensure test user follows and is followed by some users
        $usersToFollow = $users->where('id', '!=', $testUser->id)->random(5);
        foreach ($usersToFollow as $user) {
            UserFollow::firstOrCreate([
                'follower_id' => $testUser->id,
                'following_id' => $user->id,
            ], [
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);

            UserFollow::firstOrCreate([
                'follower_id' => $user->id,
                'following_id' => $testUser->id,
            ], [
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
        }
    }

    private function generatePostTitle()
    {
        $titles = [
            "How do you prevent overeating without tracking every calorie?",
            "What are the best high-protein snacks for weight loss?",
            "How many calories should I eat to maintain my current weight?",
            "Is intermittent fasting effective for long-term weight management?",
            "What are some healthy alternatives to sugar cravings?",
            "How to stay motivated when weight loss plateaus?",
            "Best exercises for burning calories at home?",
            "How to calculate calorie needs based on activity level?",
            "Are calorie-tracking apps accurate?",
            "What's the role of metabolism in weight management?",
            "How to deal with emotional eating?",
            "What are the healthiest cooking oils for calorie counting?",
            "How much water should I drink for weight loss?",
            "Can you build muscle while in a calorie deficit?",
            "What are the best low-calorie, high-volume foods?",
            "How to track calories when eating out?",
            "What's the truth about 'calories in, calories out'?",
            "How do hormones affect weight loss and calorie needs?",
            "Best practices for meal prepping for calorie control?",
            "How to break through a weight loss plateau?"
        ];

        return $titles[array_rand($titles)];
    }

    private function generatePostContent()
    {
        $contents = [
            "I've been struggling with this issue for a while and would appreciate any advice from the community. I find that tracking every single calorie is becoming obsessive and time-consuming. How do you manage to maintain a healthy diet without micromanaging every bite?",
            "I'm looking to make some changes to my diet but I'm not sure where to start. I've heard different opinions from various sources and would like to know what has worked for real people in their daily lives.",
            "As someone new to calorie counting, I'm curious about the best approaches. I want to develop sustainable habits rather than quick fixes. What strategies have helped you maintain your goals long-term?",
            "I've noticed that my weight fluctuates quite a bit even when I think I'm eating consistently. Are there factors beyond simple calorie counting that I should be considering? How do you account for these variables?",
            "With so much conflicting information online, it's hard to know what's evidence-based and what's just another fad. I'd love to hear from people who have successfully managed their weight through sustainable calorie management."
        ];

        return $contents[array_rand($contents)];
    }

    private function generateAnswerContent()
    {
        $answers = [
            "In my experience, the key is to focus on whole foods and listen to your body's hunger signals. It takes time to develop this intuition, but it's worth it for long-term success.",
            "I've found that meal prepping on Sundays helps me stay on track throughout the week without having to count every calorie. I prepare balanced meals with protein, healthy carbs, and vegetables.",
            "Don't forget the importance of hydration! Sometimes we mistake thirst for hunger. Drinking a glass of water before meals has helped me control portions naturally.",
            "I recommend using the plate method: half vegetables, quarter protein, quarter carbs. This visual approach eliminates the need for precise counting while ensuring balanced nutrition.",
            "It's helpful to remember that not all calories are equal. 100 calories of vegetables affects your body differently than 100 calories of processed sugar. Focus on food quality alongside quantity.",
            "I've had success with mindful eating practices. Eating slowly without distractions allows me to recognize when I'm full and avoid overeating.",
            "Consider working with a registered dietitian if possible. They can help you develop personalized strategies that don't require obsessive tracking.",
            "I keep a food journal without counting calories. Just writing down what I eat helps me stay accountable and notice patterns in my eating habits.",
            "Intermittent fasting has worked for me. By limiting my eating window, I naturally consume fewer calories without detailed tracking.",
            "Remember that consistency is more important than perfection. Having a rough idea of calorie ranges is often sufficient without needing to track every single calorie."
        ];

        return $answers[array_rand($answers)];
    }
}
