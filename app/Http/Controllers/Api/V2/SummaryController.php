<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TaskSummaryResource;

class SummaryController extends Controller
{
    public function __invoke(Request $request)
    {
        $tasks = $request->user()->tasksSummary($request->period);
 
        return $tasks->mapToGroups(function ($item) {
            return [
                $item->is_completed ? 'completed' : 'uncompleted' => TaskSummaryResource::make($item)
            ];
        });
    }
}