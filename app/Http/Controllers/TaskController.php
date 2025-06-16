<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponser;
use App\Http\Requests\Task\ListTaskRequest;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Requests\Task\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;

class TaskController extends Controller
{
    //
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(ListTaskRequest $request)
    {
        $tasks = $this->taskService->getDate($request->all());
        return ApiResponser::successResponse('Task Retrived',200,TaskResource::collection($tasks));
    }

    public function store(TaskRequest $request)
    {
        $task = $this->taskService->store($request->validated());
        return ApiResponser::successResponse('Task Created',200,new TaskResource($task));
    }

    public function update(TaskRequest $request,$id)
    {
        $task = $this->taskService->update($request->validated(),$id);
        return ApiResponser::successResponse('Task Updated',200,new TaskResource($task));
    }

    public function updateStatus(UpdateTaskStatusRequest $request, $id)
    {
        try {
            $task = $this->taskService->update($request->validated(), $id);
            return ApiResponser::successResponse(
                'Task Status Updated',
                200,
                new TaskResource($task)
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponser::errorResponse($e->getMessage(), 422);
        }
    }

    public function destroy($id)
    {
        $task = $this->taskService->delete($id);
        return ApiResponser::successResponse('Task Delete');
    }

    public function trashedData(ListTaskRequest $request)
    {
        $tasks = $this->taskService->getTrashedData($request->all());
        return ApiResponser::successResponse('Trashed Task Retrived',200,TaskResource::collection($tasks));
    }


    public function restore($id)
    {
        $task = $this->taskService->restore($id);
        return ApiResponser::successResponse('Task Restored');
    }    

    
    public function forceDelete($id)
    {
        $task = $this->taskService->forceDelete($id);
        return ApiResponser::successResponse('Task Deleted Permentaly');
    }    
}
