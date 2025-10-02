<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        // ➕ dodijeli rolu company_owner
        if (method_exists($user, 'assignRole')) {
            UserDetail::create([
                'user_id' => $user->id,
                'fname'   => $request->input('name', ''),
                'role'    => 'customer',
                'status'  => true,
            ]);

            // Spatie\Permission\Traits\HasRoles na User modelu
            $user->assignRole('customer');
        } elseif (Schema::hasColumn($user->getTable(), 'role')) {
            // fallback: plain kolona "role" na users tablici
            $user->forceFill(['role' => 'customer'])->save();
        }

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

}
