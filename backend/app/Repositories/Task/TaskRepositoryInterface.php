<?php
namespace App\Repositories\Task;
use App\Models\Task;
use App\Models\User;

interface TaskRepositoryInterface {
    public function listByDate(User $user, string|\Carbon\Carbon $date, ?string $sort='position', ?string $dir='asc'): \Illuminate\Support\Collection;
    public function search(User $user, string $q): \Illuminate\Support\Collection;
    public function create(User $user, array $data): Task;
    public function update(Task $task, array $data): Task;
    public function delete(Task $task): void;
    public function reorder(User $user, array $orders): void; // [{id,position}]
}