<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use App\Http\Requests\Task\{StoreTaskRequest, UpdateTaskRequest, ReorderRequest};
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(private \App\Repositories\Task\TaskRepositoryInterface $taskRepository)
    {

    }

    public function index(Request $request)
    {
        $request->validate([
            'date' => ['required','date'],
            'sort' => ['nullable','in:position,created_at'],
            'dir'  => ['nullable','in:asc,desc'],
        ]);

        $tasks = $this->taskRepository->listByDate(
            $request->user(),
            $request->input('date'),
            $request->input('sort', 'position'),
            $request->input('dir', 'asc')
        );

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskRepository->create($request->user(), $request->validated());
        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->taskRepository->update($task, $request->validated());
        return new TaskResource($task);
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskRepository->delete($task);
        return response()->noContent();
    }

    public function toggle(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task = $this->taskRepository->toggle($task);
        return TaskResource::make($task);
    }

    public function reorder(ReorderRequest $request)
    {
        $validated = $request->validated();

        $this->taskRepository->reorder(
            $request->user(),
            $validated['date'],
            $validated['orders'],
        );
        return response()->noContent();
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => ['required','string','min:1','max:500'],
        ]);

        return TaskResource::collection($this->taskRepository->search($request->user(), $request->q));
    }
}
