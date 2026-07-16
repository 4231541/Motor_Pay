<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function create(array $data): Model;
    public function findByEmail(string $email): ?Model;
    public function update(Model $user, array $data): bool;
}
