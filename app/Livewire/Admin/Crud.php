<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Crud extends Component
{
    use WithPagination;

    public $name, $email, $password, $password_confirmation, $userId;
    public $isOpen = false;  // Controls modal visibility
    public $userSearch = '';

    protected $listeners = ['delete' => 'delete'];

    public function render()
    {
        $users = User::where('name', 'like', "%{$this->userSearch}%")
                     ->orWhere('email', 'like', "%{$this->userSearch}%")
                     ->paginate(10);

        return view('livewire.admin.crud', [
            'users' => $users,
        ])
        ->extends('adminlte::page')
        ->section('content');
    }

    public function create()
    {
        $this->resetForm();
        $this->isOpen = true; // Open modal
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => $this->userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password|same:password',
        ]);

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : ($this->userId ? User::find($this->userId)->password : null),
            ]
        );

        $message = $this->userId ? 'User updated successfully.' : 'User created successfully.';
        session()->flash('message', $message);
        $this->isOpen = false; // Close modal
        $this->resetForm();
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = ''; // Do not show password when editing

        $this->isOpen = true; // Open modal
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->userId = null;
    }
}
