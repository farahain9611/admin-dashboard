<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

class Dashboard extends Component
{
    public $usersCount;

    public function mount()
    {
        $this->usersCount = User::count();
    }
    
    public function render()
    {
        return view('livewire.admin.dashboard')
        ->extends('adminlte::page')
        ->section('content');
    }
}
