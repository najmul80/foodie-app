<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(5, 10));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(2),
            'content' => '<h2>'.fake()->sentence().'</h2>'.
                '<p>'.fake()->paragraphs(3, true).'</p>'.
                '<img src="https://picsum.photos/seed/'.Str::random(5).'/800/400" alt="Blog Image" style="width:100%; border-radius: 8px; margin: 2rem 0;">'.
                '<h3>'.fake()->sentence().'</h3>'.
                '<p>'.fake()->paragraphs(2, true).'</p>'.
                '<blockquote><p>"'.fake()->sentence().'"</p></blockquote>'.
                '<p>'.fake()->paragraphs(3, true).'</p>',
            'image_path' => 'blog_posts/placeholder.jpg',
        ];
    }
}
