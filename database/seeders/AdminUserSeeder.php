<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Spatie (opcionalno)
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Osiguraj rolu 'master' (ako koristiš Spatie)
        if (class_exists(Role::class)) {
            Role::firstOrCreate(['name' => 'master', 'guard_name' => 'web']);
        }

        $users = [
            [
                'name'   => 'Filip Jankoski',
                'email'  => 'filip@agmedia.hr',
                'pass'   => 'majamaja001',
                'detail' => [
                    'fname'   => 'Filip',
                    'lname'   => 'Jankoski',
                    'address' => 'Kovačića 23',
                    'zip'     => '44320',
                    'city'    => 'Kutina',
                    'state'   => null,
                    'phone'   => null,
                    'avatar'  => 'media/avatars/default_avatar.png',
                    'bio'     => 'Lorem ipsum...',
                    'social'  => '790117367',
                    'role'    => 'master',
                    'status'  => 1,
                ],
            ],
            [
                'name'   => 'Tomislav Jureša',
                'email'  => 'tomislav@agmedia.hr',
                'pass'   => 'bakanal',
                'detail' => [
                    'fname'   => 'Tomislav',
                    'lname'   => 'Jureša',
                    'address' => 'Malešnica bb',
                    'zip'     => '10000',
                    'city'    => 'Zagreb',
                    'state'   => null,
                    'phone'   => null,
                    'avatar'  => 'media/avatars/default_avatar.png',
                    'bio'     => 'Lorem ipsum...',
                    'social'  => '',
                    'role'    => 'master',
                    'status'  => 1,
                ],
            ],
            [
                'name'   => 'Testko Testić',
                'email'  => 'test@test.hr',
                'pass'   => 'testtest001#',
                'detail' => [
                    'fname'   => 'Testko',
                    'lname'   => 'Testić',
                    'address' => 'Bebe bb',
                    'zip'     => '10000',
                    'city'    => 'Zagreb',
                    'state'   => null,
                    'phone'   => null,
                    'avatar'  => 'media/avatars/default_avatar.png',
                    'bio'     => 'Lorem ipsum...',
                    'social'  => '',
                    'role'    => 'company_owner',
                    'status'  => 1,
                ],
            ],
        ];

        foreach ($users as $u) {
            // korisnik
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'              => $u['name'],
                    'password'          => Hash::make($u['pass']),
                    'email_verified_at' => now(),
                ]
            );

            // detalji (1–1)
            $d = $u['detail'];
            UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'fname'   => $d['fname'],
                    'lname'   => $d['lname'] ?? null,
                    'address' => $d['address'] ?? null,
                    'zip'     => $d['zip'] ?? null,
                    'city'    => $d['city'] ?? null,
                    'state'   => $d['state'] ?? null,
                    'phone'   => $d['phone'] ?? null,
                    'avatar'  => $d['avatar'] ?? 'media/avatars/default_avatar.png',
                    'bio'     => $d['bio'] ?? null,
                    'social'  => $d['social'] ?? null,
                    'role'    => $d['role'] ?? 'customer',
                    'status'  => (bool)($d['status'] ?? 1),
                ]
            );

            // Spatie role (ako je instalirano)
            if (class_exists(Role::class)) {
                $user->syncRoles([$d['role']]); // npr. 'master'
            }
        }
    }
}
