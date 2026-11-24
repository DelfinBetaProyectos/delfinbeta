<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the site owner's account (registration is disabled, so this is the only way in).
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'hola@delfinbeta.tech'],
            [
                'firstname' => 'Dayan',
                'lastname' => 'Betancourt',
                'locale' => 'es',
                'password' => 'password',
            ],
        );

        $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()])->save();
    }
}
