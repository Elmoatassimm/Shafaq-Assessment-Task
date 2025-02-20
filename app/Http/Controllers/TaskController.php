<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Services\ResponseService;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Jobs\SendTaskCreatedEmail;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class TaskController extends Controller
{
    /**
     * The response service instance.
     */
    protected $responseService;

   
    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    /*
      Ensures the authenticated user is the task owner.
  
     */
    private function isTaskOwner(Task $task): void
    {
        if ($task->user_id !== Auth::id()) {
            throw new AccessDeniedHttpException('Unauthorized');
        }
    }

  
    public function index(): JsonResponse
    {
        // Retrieve tasks for the authenticated user
        $tasks = Task::where('user_id', Auth::id())->get();
        return $this->responseService->success('Tasks retrieved successfully', $tasks);
    }

  
    public function store(TaskStoreRequest $request): JsonResponse
    {
        // Retrieve validated data from the request
        $data = $request->only(['title', 'description', 'status', 'due_date']);

        // Check if a priority field is provided in the request
        if ($request->has('priority')) {
            // Verify if the authenticated user has access to the priority feature
            // using the isPaid() method on the User model.
            if (!Auth::user()->isPaid()) {
                return $this->responseService->error('Priority feature is available for paid users only',[] ,403);
            }
            $data['priority'] = $request->priority;
        }

        // Associate the task with the authenticated user
        $data['user_id'] = Auth::id();

        // Create the task record in the database
        $task = Task::create($data);

        // Dispatch the email job asynchronously
        dispatch(new SendTaskCreatedEmail($task));

        return $this->responseService->success('Task created successfully', $task, 201);
    }

  
    public function show(Task $task)
    {
        $this->isTaskOwner($task);
        return $this->responseService->success('Task retrieved successfully', $task);
    }

   
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $this->isTaskOwner($task);

        $data = $request->validated();

        // Check if the update includes a change to the priority field
        if (array_key_exists('priority', $data)) {
            // Verify if the authenticated user has access to the priority feature
            // using the isPaid() method on the User model.
            if (!Auth::user()->isPaid()) {
                return $this->responseService->error('Priority feature is available for paid users only',[] ,403);
            }
        }

        $task->update($data);

        return $this->responseService->success('Task updated successfully', $task);
    }

   
    public function destroy(Task $task)
    {
        $this->isTaskOwner($task);
        $task->delete();
        return $this->responseService->success('Task deleted successfully');
    }
}
