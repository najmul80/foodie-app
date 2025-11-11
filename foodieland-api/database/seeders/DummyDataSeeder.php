<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Recipe;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. Roles & Users ---
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'api']);
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'api']);
        $adminUser = User::firstOrCreate(['email' => 'admin@foodieland.com'], ['name' => 'Adiba Hossain (Admin)', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'bio' => 'Head Chef and Administrator.', 'profile_image' => 'profile-images/default-avatar.png']);
        $adminUser->assignRole($adminRole);
        $testUser = User::firstOrCreate(['email' => 'user@foodieland.com'], ['name' => 'Rahim Sheikh', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'bio' => 'A passionate home cook.', 'profile_image' => 'profile-images/default-avatar.png']);
        $testUser->assignRole($userRole);
        $authors = collect([$adminUser, $testUser]);
        $commentingUsers = User::factory(15)->create();
        $commentingUsers->each(fn ($user) => $user->assignRole($userRole));

        // --- 2. Categories & Tags ---
        $categoriesData = ['বাংলা রান্না', 'মিষ্টি', 'ভর্তা ও ভাজি', 'পোলাও ও বিরিয়ানি', 'মাছ', 'মাংস', 'নাস্তা', 'পানীয়', 'সকালের নাস্তা', 'স্বাস্থ্যকর', 'স্যুপ', 'নুডলস ও পাস্তা'];
        $categories = collect($categoriesData)->map(fn ($name) => Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]));
        $tagsData = ['ঝাল', 'টক', 'মিষ্টি', 'সহজ রান্না', 'ঈদের রান্না', 'ইফতার', 'পিঠা', 'ঐতিহ্যবাহী', 'ইলিশ', 'গরুর মাংস', 'চিকেন', 'উৎসব', 'দ্রুত তৈরি', 'মেহমান আপ্যায়ন', 'বাচ্চাদের প্রিয়'];
        $tags = collect($tagsData)->map(fn ($name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]));

        // --- 3. 50 Real Recipes ---
        $recipesData = [
            // Traditional Bangladeshi
            ['user_id' => $adminUser->id, 'title' => 'শর্ষে ইলিশ', 'description' => 'বাঙালির ঐতিহ্যবাহী খাবার।', 'ingredients' => ['ইলিশ মাছ', 'সরিষার তেল'], 'instructions' => [['step' => 1, 'description' => 'মাছ ও মশলা প্রস্তুত করুন।']], 'prep_time' => 15, 'cook_time' => 20, 'difficulty' => 'Medium', 'category_names' => ['বাংলা রান্না', 'মাছ'], 'tag_names' => ['ইলিশ', 'ঐতিহ্যবাহী']],
            ['user_id' => $testUser->id, 'title' => 'ঢাকাইয়া কাচ্চি বিরিয়ানি', 'description' => 'পুরান ঢাকার স্বাদের বিরিয়ানি।', 'ingredients' => ['খাসির মাংস', 'বাসমতি চাল'], 'instructions' => [['step' => 1, 'description' => 'মাংস মেরিনেট করুন।']], 'prep_time' => 300, 'cook_time' => 70, 'difficulty' => 'Hard', 'category_names' => ['পোলাও ও বিরিয়ানি', 'মাংস'], 'tag_names' => ['উৎসব', 'ঈদের রান্না']],
            ['user_id' => $adminUser->id, 'title' => 'আলু ভর্তা', 'description' => 'সহজ এবং জনপ্রিয় ভর্তা।', 'ingredients' => ['আলু সিদ্ধ', 'পেঁয়াজ কুচি'], 'instructions' => [['step' => 1, 'description' => 'সব উপকরণ একসাথে মেখে নিন।']], 'prep_time' => 5, 'cook_time' => 15, 'difficulty' => 'Easy', 'category_names' => ['ভর্তা ও ভাজি'], 'tag_names' => ['সহজ রান্না', 'দ্রুত তৈরি']],
            ['user_id' => $testUser->id, 'title' => 'চিকেন রোস্ট', 'description' => 'বিয়ের বাড়ির স্বাদের চিকেন রোস্ট।', 'ingredients' => ['মুরগির মাংস', 'টক দই', 'পেঁয়াজ বেরেস্তা'], 'instructions' => [['step' => 1, 'description' => 'মুরগি ভেজে মশলা দিয়ে রান্না করুন।']], 'prep_time' => 20, 'cook_time' => 40, 'difficulty' => 'Medium', 'category_names' => ['মাংস', 'বাংলা রান্না'], 'tag_names' => ['চিকেন', 'মেহমান আপ্যায়ন']],
            ['user_id' => $adminUser->id, 'title' => 'রূপচাঁদা মাছ ভাজা', 'description' => 'মচমচে রূপচাঁদা মাছ ভাজা।', 'ingredients' => ['রূপচাঁদা মাছ', 'হলুদ-মরিচ গুঁড়া', 'লবণ'], 'instructions' => [['step' => 1, 'description' => 'মাছ মশলা দিয়ে মেখে তেলে ভাজুন।']], 'prep_time' => 10, 'cook_time' => 15, 'difficulty' => 'Easy', 'category_names' => ['মাছ', 'ভর্তা ও ভাজি'], 'tag_names' => ['সহজ রান্না']],
            ['user_id' => $testUser->id, 'title' => 'গরুর মাংসের কালা ভুনা', 'description' => 'চট্টগ্রামের ঐতিহ্যবাহী কালা ভুনা।', 'ingredients' => ['গরুর মাংস', 'পেঁয়াজ', 'বিশেষ মশলা'], 'instructions' => [['step' => 1, 'description' => 'অনেকক্ষণ ধরে অল্প আঁচে মাংস ভাজতে থাকুন।']], 'prep_time' => 30, 'cook_time' => 120, 'difficulty' => 'Hard', 'category_names' => ['মাংস', 'বাংলা রান্না'], 'tag_names' => ['গরুর মাংস', 'ঐতিহ্যবাহী', 'ঈদের রান্না']],
            ['user_id' => $adminUser->id, 'title' => 'ভাপা পিঠা', 'description' => 'শীতের সকালের প্রধান আকর্ষণ।', 'ingredients' => ['চালের গুঁড়া', 'নারিকেল', 'গুড়'], 'instructions' => [['step' => 1, 'description' => 'ভাপে পিঠা তৈরি করুন।']], 'prep_time' => 20, 'cook_time' => 10, 'difficulty' => 'Medium', 'category_names' => ['পিঠা', 'সকালের নাস্তা'], 'tag_names' => ['পিঠা', 'ঐতিহ্যবাহী', 'মিষ্টি']],
            ['user_id' => $testUser->id, 'title' => 'সবজি খিচুড়ি', 'description' => 'বৃষ্টির দিনের জন্য উপযুক্ত খাবার।', 'ingredients' => ['পোলাওর চাল', 'মসুর ডাল', 'বিভিন্ন সবজি'], 'instructions' => [['step' => 1, 'description' => 'চাল, ডাল ও সবজি একসাথে রান্না করুন।']], 'prep_time' => 15, 'cook_time' => 30, 'difficulty' => 'Easy', 'category_names' => ['বাংলা রান্না', 'স্বাস্থ্যকর'], 'tag_names' => ['সহজ রান্না']],
            ['user_id' => $adminUser->id, 'title' => 'রসমালাই', 'description' => 'কুমিল্লার বিখ্যাত মিষ্টি।', 'ingredients' => ['ছানা', 'চিনি', 'দুধ'], 'instructions' => [['step' => 1, 'description' => 'ছোট রসগোল্লা তৈরি করে দুধের মধ্যে জ্বাল দিন।']], 'prep_time' => 40, 'cook_time' => 50, 'difficulty' => 'Hard', 'category_names' => ['মিষ্টি'], 'tag_names' => ['মিষ্টি', 'উৎসব']],
            ['user_id' => $testUser->id, 'title' => 'সিঙ্গাড়া', 'description' => 'বিকেলের নাস্তার জন্য জনপ্রিয়।', 'ingredients' => ['ময়দা', 'আলুর পুর', 'তেল'], 'instructions' => [['step' => 1, 'description' => 'সিঙ্গাড়ার ভাজ দিয়ে পুর ভরে ডুবো তেলে ভাজুন।']], 'prep_time' => 30, 'cook_time' => 20, 'difficulty' => 'Medium', 'category_names' => ['নাস্তা'], 'tag_names' => ['ঝাল']],

            // Adding 40 more...
            ['user_id' => $adminUser->id, 'title' => 'ডাল পুরি', 'description' => 'ডালের পুর ভরা মচমচে পুরি।', 'ingredients' => ['ময়দা', 'মসুর ডাল', 'তেল'], 'instructions' => [['step' => 1, 'description' => 'পুর ভরে পুরি বেলে ডুবো তেলে ভাজুন।']], 'prep_time' => 25, 'cook_time' => 15, 'difficulty' => 'Medium', 'category_names' => ['নাস্তা', 'সকালের নাস্তা'], 'tag_names' => ['সহজ রান্না']],
            ['user_id' => $testUser->id, 'title' => 'লাচ্ছি', 'description' => 'গরমের দিনের আরামদায়ক পানীয়।', 'ingredients' => ['টক দই', 'চিনি', 'পানি', 'বরফ'], 'instructions' => [['step' => 1, 'description' => 'সব উপকরণ ব্লেন্ডারে ব্লেন্ড করুন।']], 'prep_time' => 5, 'cook_time' => 0, 'difficulty' => 'Easy', 'category_names' => ['পানীয়'], 'tag_names' => ['দ্রুত তৈরি']],
            ['user_id' => $adminUser->id, 'title' => 'চিংড়ি মালাই কারি', 'description' => 'নারিকেলের দুধে রান্না করা গলদা চিংড়ি।', 'ingredients' => ['গলদা চিংড়ি', 'নারিকেলের দুধ', 'পেঁয়াজ'], 'instructions' => [['step' => 1, 'description' => 'চিংড়ি ভেজে মশলা দিয়ে রান্না করুন।']], 'prep_time' => 15, 'cook_time' => 25, 'difficulty' => 'Medium', 'category_names' => ['মাছ', 'বাংলা রান্না'], 'tag_names' => ['উৎসব', 'মেহমান আপ্যায়ন']],
            ['user_id' => $testUser->id, 'title' => 'মোরগ পোলাও', 'description' => 'আস্ত মুরগি দিয়ে তৈরি শাহী মোরগ পোলাও।', 'ingredients' => ['আস্ত মুরগি', 'পোলাওর চাল', 'পেঁয়াজ বেরেস্তা'], 'instructions' => [['step' => 1, 'description' => 'মুরগি রান্না করে পোলাওয়ের সাথে দমে দিন।']], 'prep_time' => 30, 'cook_time' => 60, 'difficulty' => 'Medium', 'category_names' => ['পোলাও ও বিরিয়ানি', 'মাংস'], 'tag_names' => ['উৎসব', 'চিকেন']],
            ['user_id' => $adminUser->id, 'title' => 'টমেটো ভর্তা', 'description' => 'পোড়া টমেটোর সুস্বাদু ভর্তা।', 'ingredients' => ['টমেটো', 'শুকনো মরিচ', 'পেঁয়াজ', 'সরিষার তেল'], 'instructions' => [['step' => 1, 'description' => 'টমেটো পুড়িয়ে খোসা ফেলে মেখে নিন।']], 'prep_time' => 5, 'cook_time' => 10, 'difficulty' => 'Easy', 'category_names' => ['ভর্তা ও ভাজি'], 'tag_names' => ['টক', 'সহজ রান্না']],
            ['user_id' => $testUser->id, 'title' => 'হালিম', 'description' => 'রমজান মাসের বিশেষ খাবার।', 'ingredients' => ['বিভিন্ন রকম ডাল', 'গম', 'মাংস'], 'instructions' => [['step' => 1, 'description' => 'ডাল ও মাংস আলাদাভাবে রান্না করে মিশিয়ে নিন।']], 'prep_time' => 60, 'cook_time' => 180, 'difficulty' => 'Hard', 'category_names' => ['মাংস', 'বাংলা রান্না'], 'tag_names' => ['ইফতার', 'ঐতিহ্যবাহী']],
            ['user_id' => $adminUser->id, 'title' => 'পাস্তা আলফ্রেডো', 'description' => 'ক্রিমি চিজ সস দিয়ে ইতালিয়ান পাস্তা।', 'ingredients' => ['পাস্তা', 'চিকেন', 'ক্রিম', 'পারমেসান চিজ'], 'instructions' => [['step' => 1, 'description' => 'পাস্তা সিদ্ধ করুন এবং সস তৈরি করুন।']], 'prep_time' => 10, 'cook_time' => 20, 'difficulty' => 'Medium', 'category_names' => ['বিদেশী রান্না', 'নুডলস ও পাস্তা'], 'tag_names' => ['চিকেন', 'বাচ্চাদের প্রিয়']],
            ['user_id' => $testUser->id, 'title' => 'থাই স্যুপ', 'description' => 'রেস্টুরেন্টের মতো থাই স্যুপ।', 'ingredients' => ['চিকেন স্টক', 'চিংড়ি', 'মাশরুম', 'থাই পাতা'], 'instructions' => [['step' => 1, 'description' => 'স্টকের সাথে সব উপকরণ মিশিয়ে ফুটিয়ে নিন।']], 'prep_time' => 10, 'cook_time' => 15, 'difficulty' => 'Easy', 'category_names' => ['স্যুপ', 'বিদেশী রান্না'], 'tag_names' => ['টক', 'ঝাল']],
            ['user_id' => $adminUser->id, 'title' => 'ফিরনি', 'description' => 'উৎসবের জন্য পারফেক্ট মিষ্টি।', 'ingredients' => ['চালের গুঁড়া', 'দুধ', 'চিনি', 'কেওড়া জল'], 'instructions' => [['step' => 1, 'description' => 'দুধ ঘন করে চালের গুঁড়া মিশিয়ে রান্না করুন।']], 'prep_time' => 5, 'cook_time' => 30, 'difficulty' => 'Easy', 'category_names' => ['মিষ্টি'], 'tag_names' => ['মিষ্টি', 'ঈদের রান্না']],
            ['user_id' => $testUser->id, 'title' => 'চিকেন নুডলস', 'description' => 'বাচ্চা এবং বড়দের সবার প্রিয়।', 'ingredients' => ['নুডলস', 'মুরগির মাংস', 'সবজি', 'সয়াসস'], 'instructions' => [['step' => 1, 'description' => 'নুডলস সিদ্ধ করে সবজির সাথে ভাজুন।']], 'prep_time' => 10, 'cook_time' => 15, 'difficulty' => 'Easy', 'category_names' => ['নুডলস ও পাস্তা', 'নাস্তা'], 'tag_names' => ['দ্রুত তৈরি', 'বাচ্চাদের প্রিয়']],
            // ... (Add 30 more similar entries here to reach 50)
        ];

        // This loop will create the 20 recipes defined above
        foreach ($recipesData as $data) {
            $recipe = Recipe::firstOrCreate(['slug' => Str::slug($data['title'])], [
                'user_id' => $data['user_id'], 'title' => $data['title'], 'description' => $data['description'],
                'ingredients' => $data['ingredients'], 'instructions' => $data['instructions'],
                'prep_time' => $data['prep_time'], 'cook_time' => $data['cook_time'], 'difficulty' => $data['difficulty'],
                'image_path' => 'recipes/placeholder.jpg',
            ]);
            $categoryIds = $categories->whereIn('name', $data['category_names'])->pluck('id');
            $tagIds = $tags->whereIn('name', $data['tag_names'])->pluck('id');
            $recipe->categories()->sync($categoryIds);
            $recipe->tags()->sync($tagIds);
        }

        // Use the factory to generate the rest of the recipes to reach 50
        $remainingRecipes = 50 - count($recipesData);
        if ($remainingRecipes > 0) {
            Recipe::factory($remainingRecipes)->create()->each(function ($recipe) use ($categories, $tags) {
                $recipe->categories()->attach($categories->random(rand(1, 2))->pluck('id')->toArray());
                $recipe->tags()->attach($tags->random(rand(2, 4))->pluck('id')->toArray());
            });
        }

        // --- 5. Create Blog Posts & Comments ---
        BlogPost::factory(15)->create(['user_id' => $authors->random()->id]);
        Recipe::all()->each(fn ($r) => $r->comments()->saveMany(Comment::factory(rand(0, 3))->make(['user_id' => $commentingUsers->random()->id])));
        BlogPost::all()->each(fn ($p) => $p->comments()->saveMany(Comment::factory(rand(0, 3))->make(['user_id' => $commentingUsers->random()->id])));

        // --- 6. Create Settings ---
        Setting::updateOrCreate(['key' => 'about_page'], ['value' => json_encode(['recipe_count' => '50+', 'community_count' => '12+', 'contributor_count' => '2+', 'team_members' => [['name' => 'Adiba Hossain', 'role' => 'Founder & CEO', 'image' => 'https://i.pravatar.cc/150?img=1']]])]);
        Setting::updateOrCreate(['key' => 'contact_page'], ['value' => json_encode(['address' => 'House 12, Road 5, Dhanmondi<br>Dhaka, 1205', 'email' => 'contact@foodieland.com.bd', 'phone' => '+880 1712 345678', 'faqs' => [['question' => 'How to submit a recipe?', 'answer' => 'Use the contact form.']]])]);
    }
}
