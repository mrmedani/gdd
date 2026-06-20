<?php

namespace App\Livewire;

use App\Shared\Livewire\WithToast;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileSettings extends Component
{
    use WithFileUploads, WithToast;

    public $name;
    public $email;
    public $photo;

    public $whatsapp_phone;
    public $notifyWhatsapp = false;
    
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->whatsapp_phone = $user->whatsapp_phone;
        $this->notifyWhatsapp = $user->notify_whatsapp ?? false;
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => 'nullable|image|max:5120',
            'whatsapp_phone' => 'nullable|string|max:20',
            'notifyWhatsapp' => 'boolean',
        ]);

        $user->name = $this->name;
        $user->email = $this->email;
        $user->whatsapp_phone = $this->whatsapp_phone;
        $user->notify_whatsapp = $this->notifyWhatsapp;

        if ($this->photo) {
            try {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                $user->photo = $this->photo->store('profiles', 'public');
            } catch (\Exception $e) {
                return;
            }
        }

        $user->save();
        $this->notify(__('common.saved'));
    }

    public function toggleWhatsApp()
    {
        $user = auth()->user();

        $enable = !$this->notifyWhatsapp;

        if ($enable && empty($user->whatsapp_phone) && empty($this->whatsapp_phone)) {
            $this->notify(__('profile.whatsapp_phone_required'), 'error');
            return;
        }

        $this->notifyWhatsapp = $enable;
        $user->notify_whatsapp = $enable;
        $user->save();

        $this->notify(__('common.saved'));
    }

    public function updateWhatsAppPhone()
    {
        $this->validate([
            'whatsapp_phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->whatsapp_phone = $this->whatsapp_phone;
        $user->save();

        $this->notify(__('common.saved'));
    }

    public function removePhoto()
    {
        $user = auth()->user();
        
        if ($user->photo) {
            try {
                Storage::disk('public')->delete($user->photo);
            } catch (\Exception $e) {
                //
            }
            $user->photo = null;
            $user->save();
        }
        
        $this->photo = null;
        $this->notify(__('common.saved'));
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->notify(__('common.saved'));
    }

    public function render()
    {
        $maxPhotoSize = 5120;
        return view('livewire.profile-settings', compact('maxPhotoSize'))->layout('layouts.app');
    }
}
