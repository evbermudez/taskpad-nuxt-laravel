<?php

namespace App\Repositories\Task;

use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function listByDate(
        User $user,
        Carbon|string $date,
        ?string $sort = 'position',
        ?string $dir = 'asc'
    ): Collection;

    public function search(User $user, string $q): Collection;

    public function create(User $user, array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): void;

    public function toggle(Task $task): Task;

    public function reorder(User $user, Carbon|string $date, array $orders): void;
}
