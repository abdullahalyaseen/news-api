<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Factories\UserFactory;
use Faker\Factory;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(50)->create();
        Category::factory(15)->create();
        Post::factory(500)->create();
        Comment::factory(1500)->create();
    }
}
