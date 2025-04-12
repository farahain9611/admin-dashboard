<div>
    <!-- Back Button -->
    <a href="javascript:history.back()" class="btn btn-outline-secondary mb-3 btn-sm">← Back</a>

    <!-- Search and Actions -->
    <div class="d-flex justify-content-end mb-2 gap-2">
        <input type="text" wire:model.debounce.300ms="userSearch" class="form-control w-25" placeholder="Search users...">
        &nbsp;
        <button wire:click="create" class="btn btn-success btn-sm">+ User</button>
        &nbsp;
        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.user-roles') }}" class="btn btn-sm btn-dark">Manage Roles</a>
        @endif
    </div>

    <!-- Users Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 40%">Name</th>
                <th style="width: 40%">Email</th>
                <th style="width: 20%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <button wire:click="edit({{ $user->id }})" class="btn btn-primary btn-sm">Edit</button>
                        <button wire:click="confirmDelete({{ $user->id }})" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}

    <!-- Modal (Add/Edit User) -->
    @if($isOpen)
        <div class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5)" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">{{ $userId ? 'Edit User' : 'Add User' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('isOpen', false)" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model.defer="name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model.defer="email">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" wire:model.defer="password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        @if(!$userId) <!-- Only show confirmation for new users -->
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" wire:model.defer="password_confirmation">
                                @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('isOpen', false)" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" wire:click="store">Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
