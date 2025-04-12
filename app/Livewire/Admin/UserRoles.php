<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserRoles extends Component
{
    use WithPagination;

    public $selectedUser, $selectedRole, $selectedRoleForPermission;
    public $userSearch = '', $roleSearch = '';
    public $newRoleName, $newPermissionName;
    public $showCreateRoleModal = false, $showCreatePermissionModal = false;
    public $confirmingDelete = false, $deleteType, $deleteTarget;
    public $rolePermissions = [], $userPermissions = [], $userRoles = [];

    public $editingRole = null, $editingPermission = null;
    public $editRoleName = '', $editPermissionName = '';

    public function updatedSelectedRoleForPermission($value)
    {
        $this->loadRolePermissions();
    }

    public function updatedSelectedUser($value)
    {
        $this->loadUserRolesAndPermissions();
    }

    public function loadRolePermissions()
    {
        if ($this->selectedRoleForPermission) {
            $role = Role::where('name', $this->selectedRoleForPermission)->first();
            if ($role) {
                $this->rolePermissions = $role->permissions->pluck('name')->toArray();
            }
        }
    }

    public function loadUserRolesAndPermissions()
    {
        if ($this->selectedUser) {
            $user = User::find($this->selectedUser);
            $this->userRoles = $user->getRoleNames()->toArray();
            $this->userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        } else {
            $this->userRoles = [];
            $this->userPermissions = [];
        }
    }

    public function toggleRolePermission($permissionName)
    {
        $role = Role::where('name', $this->selectedRoleForPermission)->first();
        if ($role) {
            $role->hasPermissionTo($permissionName)
                ? $role->revokePermissionTo($permissionName)
                : $role->givePermissionTo($permissionName);

            $this->loadRolePermissions();
        }
    }

    public function assignRole()
    {
        $this->validate([
            'selectedUser' => 'required|exists:users,id',
            'selectedRole' => 'required|exists:roles,name',
        ]);

        $user = User::find($this->selectedUser);
        $user->assignRole($this->selectedRole);
        $this->loadUserRolesAndPermissions();
        session()->flash('message', 'Role assigned successfully.');
    }

    public function removeUserRole($roleName)
    {
        $user = User::find($this->selectedUser);
        if ($user) {
            $user->removeRole($roleName);
            $this->loadUserRolesAndPermissions();
            session()->flash('message', 'Role removed successfully.');
        }
    }

    public function storeRole()
    {
        $this->validate([
            'newRoleName' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create(['name' => $this->newRoleName]);
        session()->flash('message', 'Role created successfully.');
        $this->reset(['newRoleName', 'showCreateRoleModal']);
    }

    public function storePermission()
    {
        $this->validate([
            'newPermissionName' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create(['name' => $this->newPermissionName]);
        session()->flash('message', 'Permission created successfully.');
        $this->reset(['newPermissionName', 'showCreatePermissionModal']);
    }

    public function confirmDelete($type, $target)
    {
        $this->confirmingDelete = true;
        $this->deleteType = $type;
        $this->deleteTarget = $target;
    }

    public function deleteConfirmed()
    {
        if ($this->deleteType === 'role') {
            $role = Role::findByName($this->deleteTarget);
            $role->delete();
        } elseif ($this->deleteType === 'permission') {
            $permission = Permission::findByName($this->deleteTarget);
            $permission->delete();
        }

        $this->confirmingDelete = false;
        session()->flash('message', ucfirst($this->deleteType).' deleted successfully.');
    }

    public function editRole($roleName)
    {
        $this->editingRole = $roleName;
        $this->editRoleName = $roleName;
    }

    public function updateRole()
    {
        $this->validate([
            'editRoleName' => 'required|string|max:255|unique:roles,name,' . $this->editingRole . ',name',
        ]);

        $role = Role::findByName($this->editingRole);
        $role->name = $this->editRoleName;
        $role->save();

        session()->flash('message', 'Role updated successfully.');
        $this->reset(['editingRole', 'editRoleName']);
    }

    public function editPermission($permissionName)
    {
        $this->editingPermission = $permissionName;
        $this->editPermissionName = $permissionName;
    }

    public function updatePermission()
    {
        $this->validate([
            'editPermissionName' => 'required|string|max:255|unique:permissions,name,' . $this->editingPermission . ',name',
        ]);

        $permission = Permission::findByName($this->editingPermission);
        $permission->name = $this->editPermissionName;
        $permission->save();

        session()->flash('message', 'Permission updated successfully.');
        $this->reset(['editingPermission', 'editPermissionName']);
    }

    public function render()
    {
        $users = User::where(function ($query) {
            $query->where('name', 'like', "%{$this->userSearch}%")
                  ->orWhere('email', 'like', "%{$this->userSearch}%");
        })->paginate(5);

        $roles = Role::where('name', 'like', "%{$this->roleSearch}%")->get();

        $permissions = Permission::all();

        return view('livewire.admin.user-roles', [
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
        ])->extends('adminlte::page')->section('content');
    }
}
