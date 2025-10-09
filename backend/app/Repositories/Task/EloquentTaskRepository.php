<?php
namespace App\Repositories\Task;
use App\Models\Task;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function listByDate(User $user, $date, ?string $sort='position', ?string $dir='asc')
    {
        return Task::where('user_id', $user->id)
            ->whereDate('task_date', \Carbon\Carbon::parse($date))
            ->orderBy($sort, $dir)
            ->get();
    }

    public function search(User $user, string $q): \Illuminate\Support\Collection
    {
        return Task::where('user_id', $user->id)
            ->where('statement', 'like', "%{$q}%")
            ->orderBy('task_date', 'desc')
            ->orderBy('position')
            ->get();
    }

    public function create(User $user, array $data): Task
    {
        $max = Task::where('user_id', $user->id)
            ->whereDate('task_date', $data['task_date'])
            ->max('position');

        $data['position'] = is_null($max) ? 0 : $max + 1;

        return $user->tasks()->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function reorder(User $user, array $orders): void
    {
        DB::transaction(function () use ($orders, $user) {
            foreach ($orders as $row) {
                Task::where('id', $row['id'])
                    ->where('user_id', $user->id)
                    ->update(['position' => $row['position']]);
            }
        });
    }
}