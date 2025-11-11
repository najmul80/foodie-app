<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Import the User model

class RecipeFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(4, 7));

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(3),
            'ingredients' => [
                // THIS IS THE FIX: Replaced 'foodName()' with 'word()'
                ucfirst(fake()->word()).' '.fake()->word().', '.rand(1, 4).' cups',
                ucfirst(fake()->word()).', '.rand(1, 2).' tbsp',
                'Onion, '.rand(1, 3).' chopped',
                'Garlic, '.rand(2, 5).' cloves, minced',
                'Salt, to taste',
            ],
            'instructions' => [
                ['step' => 1, 'description' => 'First, prepare all of your ingredients and preheat your oven to '.rand(180, 220).'°C.', 'image' => 'https://picsum.photos/seed/'.Str::random(5).'/600/400.jpg'],
                ['step' => 2, 'description' => 'In a large bowl, mix together the wet and dry ingredients until just combined. Do not overmix.', 'image' => null],
                ['step' => 3, 'description' => 'Pour the mixture into a prepared baking dish and bake for '.rand(25, 45).' minutes.', 'image' => 'https://picsum.photos/seed/'.Str::random(5).'/600/400.jpg'],
            ],
            'image_path' => 'recipes/placeholder.jpg',
            'prep_time' => fake()->numberBetween(10, 30),
            'cook_time' => fake()->numberBetween(20, 90),
            'difficulty' => fake()->randomElement(['Easy', 'Medium', 'Hard']),
            'nutrition_facts' => [
                'calories' => fake()->numberBetween(200, 800).' kcal',
                'protein' => fake()->numberBetween(10, 50).' g',
            ],
        ];
    }
}
