<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Admin\Users\AssignUserRolesRequest;
use App\Http\Requests\Api\V1\Admin\Users\SearchUserRequest;
use App\Http\Requests\Api\V1\Admin\Users\StoreUserRequest;
use App\Http\Requests\Api\V1\Admin\Users\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Services\AdminUserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AdminUserController extends ApiController
{
    use ApiResponse;

    public function __construct(private readonly AdminUserService $userService) {}

    /**
     * Display a listing of users with search and filter.
     */
    public function index(SearchUserRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $users = $this->userService->getUsers($request->validated(), $perPage);

        return $this->success(UserResource::collection($users)->response()->getData(true));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return $this->success(['user' => new UserResource($user)], 'User created successfully.', 201);
    }

    /**
     * Display the specified user.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        
        Gate::authorize('view', $user);

        return $this->success(new UserResource($user));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        
        Gate::authorize('update', $user);

        $user = $this->userService->updateUser($user, $request->validated());

        return $this->success(['user' => new UserResource($user)], 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        
        Gate::authorize('delete', $user);

        $this->userService->deleteUser($user);

        return $this->success(null, 'User deleted successfully.');
    }

    /**
     * Assign roles to a user.
     */
    public function assignRoles(AssignUserRolesRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        
        Gate::authorize('assignRoles', $user);

        $user = $this->userService->syncRoles($user, $request->input('roles'));

        return $this->success(new UserResource($user), 'Roles assigned successfully.');
    }
}
