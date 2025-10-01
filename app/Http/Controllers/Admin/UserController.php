<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $role  = $request->string('role')->toString();
        if ( ! $role) {
            $role = 'company_owner';

            return redirect()->route('users.index', ['role' => $role]);
        }
        $users = UserDetail::with('user')
                           ->where('role', $role)
                           ->latest('updated_at')
                           ->get();

        return view('admin.users.index', compact('users', 'role'));
    }


    public function create()
    {
        return view('admin.users.edit'); // koristimo isti view kao edit
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:master,admin,manager,editor,company_owner,customer'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        UserDetail::create([
            'user_id' => $user->id,
            'fname'   => $request->input('fname', ''),
            'lname'   => $request->input('lname', ''),
            'address' => $request->input('address'),
            'zip'     => $request->input('zip'),
            'city'    => $request->input('city'),
            'state'   => $request->input('state'),
            'phone'   => $request->input('phone'),
            'role'    => $validated['role'],
            'status'  => $request->boolean('status', true),
        ]);

        return redirect()->route('users.index')->with('success', 'Korisnik kreiran.');
    }


    public function edit(User $user)
    {
        $user->load('detail');

        return view('admin.users.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', 'in:master,admin,manager,editor,company_owner,customer'],
        ]);

        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email']
        ]);

        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'fname'   => $request->input('fname', ''),
                'lname'   => $request->input('lname'),
                'address' => $request->input('address'),
                'zip'     => $request->input('zip'),
                'city'    => $request->input('city'),
                'state'   => $request->input('state'),
                'phone'   => $request->input('phone'),
                'role'    => $validated['role'],
                'status'  => $request->boolean('status', true),
            ]
        );

        return redirect()->route('users.index')->with('success', 'Korisnik ažuriran.');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Korisnik obrisan.');
    }
}
