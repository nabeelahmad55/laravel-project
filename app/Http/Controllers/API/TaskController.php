<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['assignee', 'project', 'creator'])
                     ->filter($request->only(['status', 'priority', 'assigned_to']))
                     ->orderBy('position')
                     ->orderBy('created_at', 'desc');

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $request->has('per_page') 
            ? $query->paginate($request->per_page)
            : $query->get();

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
            'created_by' => auth()->id(),
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'position' => Task::where('project_id', $request->project_id)->max('position') + 1
        ]);

        // Broadcast event for real-time updates
        broadcast(new TaskCreated($task))->toOthers();

        return new TaskResource($task->load(['assignee', 'project']));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return new TaskResource($task->load(['assignee', 'project', 'comments.user', 'attachments']));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        if ($request->has('status') && $request->status === 'completed') {
            $task->update(['completed_at' => now()]);
        }

        broadcast(new TaskUpdated($task))->toOthers();

        return new TaskResource($task->fresh());
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        broadcast(new TaskDeleted($task))->toOthers();

        return response()->noContent();
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'exists:tasks,id',
            'tasks.*.position' => 'integer'
        ]);

        foreach ($request->tasks as $taskData) {
            Task::where('id', $taskData['id'])
                ->update(['position' => $taskData['position']]);
        }

        return response()->json(['message' => 'Tasks reordered successfully']);
    }
}