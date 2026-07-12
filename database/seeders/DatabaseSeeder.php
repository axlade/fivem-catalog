<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'is_verified' => true,
        ]);

        $creator = User::factory()->create([
            'name' => 'Test Creator',
            'username' => 'testcreator',
            'email' => 'creator@example.com',
            'role' => 'creator',
            'is_verified' => true,
        ]);

        $tags = collect(['Optimized', 'Configurable', 'Lightweight'])
            ->map(fn (string $name) => Tag::create(['name' => $name]));

        Resource::factory(12)
            ->create(['user_id' => $creator->id])
            ->each(fn (Resource $resource) => $resource->tags()->attach($tags->random(rand(1, 2))));

        Resource::factory(3)
            ->create(['user_id' => $creator->id, 'status' => 'pending']);
    }
}
