<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\Task\TaskRepositoryInterface;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskRepositoryInterface $repo)
    {
    }

    public function index(Request $request)
    {
        $request->validate([
            'date' => ['required','date'],
            'sort' => ['nullable','in:position,priority,created_at'],
            'dir'  => ['nullable','in:asc,desc'],
        ]);

        $items = $this->repo->listByDate(
            $request->user(),
            $request->date,
            $request->input('sort', 'position'),
            $request->input('dir', 'asc'),
        );

        return TaskResource::collection($items);
    }


    public function store(StoreTaskRequest $request)
    {
        $task = $this->repo->create($request->user(), $request->validated());
        return TaskResource::make($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task = $this->repo->update($task, $request->validated());
        return TaskResource::make($task);
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);
        $this->repo->delete($task);
        return response()->noContent();
    }

    public function toggle(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task = $this->repo->toggle($task);
        return TaskResource::make($task);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'date'   => ['required','date'],
            'orders' => ['required','array'],
            'orders.*.id'       => ['required','integer','exists:tasks,id'],
            'orders.*.position' => ['required','integer'],
        ]);

        $this->repo->reorder($request->user(), $data['date'], $data['orders']);
        return response()->noContent();
    }

    public function search(Request $request)
    {
        $request->validate(['q' => ['required','string','max:100']]);
        $items = $this->repo->search($request->user(), $request->q);
        return TaskResource::collection($items);
    }
}
