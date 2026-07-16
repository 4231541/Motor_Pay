<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class AdminUserService
{
    /**
     * Get paginated users with optional search and filters.
     */
    public function getUsers(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles');

        // Keyword search on name, email, phone
        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%")
                  ->orWhere('phone', 'LIKE', "%{$keyword}%");
            });
        }

        // Filter by role name
        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find a user by ID or fail.
     */
    public function findOrFail(int $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if (!empty($data['roles'])) {
            $this->syncRoles($user, $data['roles']);
        }

        return $user->load('roles');
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->refresh()->load('roles');
    }

    /**
     * Delete a user (soft delete).
     */
    public function deleteUser(User $user): void
    {
        // Revoke all tokens before soft-deleting
        $user->tokens()->delete();
        $user->delete();
    }

    /**
     * Sync roles for a user.
     */
    public function syncRoles(User $user, array $roleNames): User
    {
        $roleIds = Role::whereIn('name', $roleNames)->pluck('id');
        $user->roles()->sync($roleIds);
        $user->unsetRelation('roles');

        return $user->load('roles');
    }
}
