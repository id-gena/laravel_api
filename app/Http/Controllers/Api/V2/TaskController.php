<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return request()->user()
            ->tasks()
            ->handleSort(request()->query('sort_by') ?? 'time')
            ->handleFilter(request()->query('due_date'))
            ->with('priority')
            ->get()
            ->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     *
     * Return it as a Resource in order to be used by API.
     */
    public function store(StoreTaskRequest $request)
    {
        if ($request->user()->cannot('create', Task::class)) {
            abort(403);
        }
        $task = $request->user()->tasks()->create($request->validated());
        $task->load('priority');

        return $task->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task, Request $request)
    {
        if ($request->user()->cannot('view', $task)) {
            abort(403);
        }
        $task->load('priority');
        return $task->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($request->user()->cannot('update', $task)) {
            abort(403);
        }
        
        $task->update($request->validated());
        $task->load('priority');
    
        return $task->toResource();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, Request $request)
    {
        if ($request->user()->cannot('delete', $task)) {
            abort(403);
        }
        $task->delete();

        return response()->noContent();
    }
}
