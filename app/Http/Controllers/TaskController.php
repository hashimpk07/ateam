<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        try{
            $query = Task::query();
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            // Filter by due_date
            if ($request->has('due_date')) {
                $query->whereDate('due_date', $request->due_date);
            }
            return response()->json([
                'status'  => true,
                'message' => 'Task fetched successfully',
                'data'    => TaskResource::collection($query->latest()->get()),
            ], 200);
           
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([   
                'status' => false,
                   'message' => 'Task fetched failed',
                'errors' => $e->errors()
            ]);
        } 
    }

    public function store(TaskRequest $request)
    {
        try{
            $task = Task::create($request->validated());
            return response()->json([
                'status'  => true, 
                'message' => 'Task retrieved successfully',
                'data'    =>  new TaskResource($task),
            ], 200);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422); 
        }
    }

    public function show($id)
    {
        try{
            $task = Task::findOrFail($id);
            return response()->json([
                'status'  => true, 
                'message' => 'Task retrieved successfully',
                'data'    =>  new TaskResource($task),
            ], 200);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status'=>'error']);
        } 
    }

    public function update(TaskRequest $request, $id)
    {
        try{
            $task = Task::findOrFail($id);
            $task->update($request->validated());
            return response()->json([
                'status'  => true, 
                'message' => 'Task updated  successfully',
                'data'    =>  new TaskResource($task),
            ], 200);
            return new TaskResource($task);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status'=>'error']);
        } 
    }

    public function destroy($id)
    {
        try{
            $task = Task::findOrFail($id);
            $task->delete();
            return response()->json([
                'status'  => true, 
                'message' => 'Task deleted  successfully',
                'data'    =>  new TaskResource($task),
            ], 200);
            return response()->json(['message' => 'Task deleted']);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status'=>'error']);
        } 
    }
}
