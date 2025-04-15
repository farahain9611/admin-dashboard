<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Crud;
use App\Livewire\Admin\UserRoles;
use Spatie\Permission\Middleware\RoleMiddleware;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
// Route::view('/', 'welcome')->name('welcome');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout'); // Logout route (fix)
Route::get('/', function () {
    return redirect()->route('login'); // Redirect to login page
});
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/users', Crud::class)->name('admin.users');
});
Route::middleware('role:admin')->group(function () {
    Route::get('/admin/user-roles', UserRoles::class)->name('admin.user-roles');
});
// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth'])
//     ->name('dashboard');
// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

Route::get('/health', function () {
    return response()->json(['status' => 'OK'], 200);
});
require __DIR__.'/auth.php';
