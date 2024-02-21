<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Gift;
use App\Models\GiftsBag;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
        User::factory(2)
            ->state(new Sequence(
                fn ($sequence) => ['email' => 'user'.$sequence->index.'@exemple.com']))
            ->has(GiftsBag::factory(1)->has(Gift::factory(5)))
            ->has(Wallet::factory(1))
            ->create();
    }
}
