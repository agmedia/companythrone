<?php

namespace App\Livewire\Settings;

use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.auth')]
class UserDetails extends Component
{
    use WithFileUploads;

    // polja
    public string $fname = '';
    public ?string $lname = null;
    public ?string $address = null;
    public ?string $zip = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $phone = null;
    public ?string $bio = null;
    public ?string $social = null;
    public string $role = 'customer';
    public bool $status = true;

    public ?string $avatar = null;               // spremljeni path
    public $avatarUpload;                        // temp upload

    public function mount(): void
    {
        $user = Auth::user();
        $d = $user?->detail;

        if ($d) {
            $this->fill([
                'fname' => $d->fname,
                'lname' => $d->lname,
                'address' => $d->address,
                'zip' => $d->zip,
                'city' => $d->city,
                'state' => $d->state,
                'phone' => $d->phone,
                'avatar' => $d->avatar,
                'bio' => $d->bio,
                'social' => $d->social,
                'role' => $d->role,
                'status' => (bool)$d->status,
            ]);
        }
    }

    public function save(): void
    {
        $this->validate([
            'fname' => ['required','string','max:255'],
            'lname' => ['nullable','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'zip' => ['nullable','string','max:50'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'avatarUpload' => ['nullable','image','max:1024'], // ~1MB
            'bio' => ['nullable','string'],
            'social' => ['nullable','string','max:255'],
            'role' => ['required','in:master,admin,manager,editor,customer'],
            'status' => ['boolean'],
        ]);

        $user = Auth::user();

        // upload avatara ako je odabran
        $avatarPath = $this->avatar;
        if ($this->avatarUpload) {
            $stored = $this->avatarUpload->store('avatars', 'public'); // public/storage/avatars/...
            $avatarPath = 'storage/'.$stored;
        }

        UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'fname' => $this->fname,
                'lname' => $this->lname,
                'address' => $this->address,
                'zip' => $this->zip,
                'city' => $this->city,
                'state' => $this->state,
                'phone' => $this->phone,
                'avatar' => $avatarPath ?? 'media/avatars/default_avatar.png',
                'bio' => $this->bio,
                'social' => $this->social,
                'role' => $this->role,
                'status' => $this->status,
            ]
        );

        session()->flash('status', 'user-details-updated');
    }

    public function render()
    {
        return view('livewire.settings.user-details');
    }
}
