<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Requests\CompleteTaskRequest;

class CompleteTaskController extends Controller
{
    /**
     * Handle the incoming request.
     * Updating the is_completed field.
     */
    public function __invoke(CompleteTaskRequest $request, Task $task)
    {
        $task->is_completed = $request->is_completed;
        $task->save();

        return $task->toResource();
    }
}
