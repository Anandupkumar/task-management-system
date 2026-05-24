<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use App\Models\User;

interface TaskRepositoryInterface
{
    /**
     * Get all tasks, with optional filters and user scoping.
     *
     * @param array $filters
     * @param User|null $user
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(array $filters = [], ?User $user = null);

    /**
     * Find a task by its ID.
     *
     * @param int $id
     * @return Task
     */
    public function find(int $id): Task;

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task;

    /**
     * Update an existing task.
     *
     * @param int $id
     * @param array $data
     * @return Task
     */
    public function update(int $id, array $data): Task;

    /**
     * Delete a task.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;
}
