<?php
namespace App\Repositories\Task;
use App\Models\{Task, User};
use Carbon\Carbon;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\DB;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function listByDate(
        User $user,
        Carbon|string $date,
        ?string $sort='position',
        ?string $dir='asc'
        ): Collection
    {
        $day = $date instanceof Carbon ? $date : Carbon::parse($date);

        return Task::query()
            ->where('user_id', $user->id)
            ->whereDate('task_date', $day)
            ->orderBy($sort, $dir)
            ->orderBy('id')
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
        $data['user_id'] = $user->id;
        $data['position'] = $data['position'] ?? (($user->tasks()->max('position') ?? 0) + 1);
        return Task::create($data);
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

    public function toggle(Task $task): Task
    {
        $task->is_done = !$task->is_done;
        $task->save();
        return $task->refresh();
    }

    public function reorder(User $user, Carbon|string $date, array $orders): void
    {
        $day = $date instanceof Carbon ? $date : Carbon::parse($date);

        $map = collect($orders)->keyBy('id');
        Task::query()
            ->where('user_id', $user->id)
            ->whereDate('task_date', $day)
            ->get()
            ->each(function (Task $t) use ($map) {
                if ($map->has($t->id)) {
                    $t->position = (int) $map[$t->id]['position'];
                    $t->save();
                }
            });
    }
}