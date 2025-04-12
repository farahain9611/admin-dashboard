<div class="container py-4">
<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3">
    ← Back
</a>
    <div class="row">
        <div class="col-md-6">
            <h4>Users</h4>
            <input type="text" wire:model.debounce.300ms="userSearch" class="form-control mb-2" placeholder="Search users by name/email">
            <ul class="list-group">
                @foreach($users as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $user->name }}</strong> <br> <small>{{ $user->email }}</small>
                        </div>
                        <button class="btn btn-sm btn-dark" wire:click="$set('selectedUser', {{ $user->id }})">Manage</button>
                    </li>
                @endforeach
            </ul>
            <div class="mt-2">
                {{ $users->links() }}
            </div>
        </div>

        <div class="col-md-6">
            @if($selectedUser)
                <div class="mb-3">
                    <button class="btn btn-outline-secondary btn-sm" wire:click="$set('selectedUser', null)">
                        ← Back to Users List
                    </button>
                </div>

                <h4>Manage Roles & Permissions</h4>
                <p><strong>User:</strong> {{ \App\Models\User::find($selectedUser)?->name }}</p>

                <div class="mb-2">
                    <label>Assign Role</label>
                    <select class="form-control" wire:model="selectedRole">
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary mt-2" wire:click="assignRole">Assign</button>
                </div>

                <div class="mb-2">
                    <label>User Roles</label>
                    <ul>
                        @foreach($userRoles as $role)
                            <li>{{ $role }} <button class="btn btn-sm btn-danger" wire:click="removeUserRole('{{ $role }}')">Remove</button></li>
                        @endforeach
                    </ul>
                </div>

                <div class="mb-2">
                    <label>User Permissions</label>
                    <ul>
                        @foreach($userPermissions as $perm)
                            <li>{{ $perm }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <h4>Roles <button class="btn btn-sm btn-success" wire:click="$set('showCreateRoleModal', true)">+ Role</button></h4>
            <input type="text" wire:model.debounce.300ms="roleSearch" class="form-control mb-2" placeholder="Search roles">
            <ul class="list-group">
                @foreach($roles as $role)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $role->name }}</strong>
                                @if($editingRole === $role->name)
                                    <input type="text" wire:model.defer="editRoleName" class="form-control mt-1">
                                    <button class="btn btn-sm btn-success mt-1" wire:click="updateRole">Save</button>
                                @else
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-info" wire:click="editRole('{{ $role->name }}')">Edit</button>
                                        <button class="btn btn-sm btn-secondary" wire:click="$set('selectedRoleForPermission', '{{ $role->name }}')">Permissions</button>
                                        <button class="btn btn-sm btn-danger" wire:click="confirmDelete('role', '{{ $role->name }}')">Delete</button>
                                    </div>
                                @endif
                            </div>
                            @if($selectedRoleForPermission === $role->name)
                                <div class="w-100 mt-2">
                                    <div class="mb-2">
                                        <button class="btn btn-outline-secondary btn-sm" wire:click="$set('selectedRoleForPermission', null)">
                                            ← Back to Roles List
                                        </button>
                                    </div>
                                    <p><strong>Permissions:</strong></p>
                                    @foreach($permissions as $perm)
                                        <label class="d-block">
                                            <input type="checkbox" wire:click="toggleRolePermission('{{ $perm->name }}')" @if(in_array($perm->name, $rolePermissions)) checked @endif>
                                            {{ $perm->name }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-6">
            <h4>Permissions <button class="btn btn-sm btn-success" wire:click="$set('showCreatePermissionModal', true)">+ Permission</button></h4>
            <ul class="list-group">
                @foreach($permissions as $permission)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>
                                @if($editingPermission === $permission->name)
                                    <input type="text" wire:model.defer="editPermissionName" class="form-control">
                                    <button class="btn btn-sm btn-success mt-1" wire:click="updatePermission">Save</button>
                                @else
                                    {{ $permission->name }}
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-info" wire:click="editPermission('{{ $permission->name }}')">Edit</button>
                                        <button class="btn btn-sm btn-danger" wire:click="confirmDelete('permission', '{{ $permission->name }}')">Delete</button>
                                    </div>
                                @endif
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Modals -->
    @if($showCreateRoleModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Role</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showCreateRoleModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" wire:model.defer="newRoleName" class="form-control" placeholder="Role name">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showCreateRoleModal', false)">Cancel</button>
                        <button class="btn btn-success" wire:click="storeRole">Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showCreatePermissionModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Permission</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showCreatePermissionModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" wire:model.defer="newPermissionName" class="form-control" placeholder="Permission name">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showCreatePermissionModal', false)">Cancel</button>
                        <button class="btn btn-success" wire:click="storePermission">Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this {{ $deleteType }}: <strong>{{ $deleteTarget }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteConfirmed">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .btn-outline-secondary {
        border-radius: 50px;
    }

    .btn-primary, .btn-info {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover, .btn-info:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .table th, .table td {
        vertical-align: middle;
    }

    .table-primary {
        background-color: #e2e6ea;
    }

    .modal-content {
        border-radius: 1rem;
    }

    .modal-header {
        background-color: #f8f9fa;
    }
</style>
@endpush
