<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

interface UserRepository
{
    public function create(array $data): User;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByIdDocument(string $idDocument): ?User;
    public function findByFilters(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator;
}
