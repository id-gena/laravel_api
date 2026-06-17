<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Priority;
 
class PriorityController extends Controller
{
    public function __invoke(Request $request)
    {
        return Priority::all()->toResourceCollection();
    }
}
