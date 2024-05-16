<?php

namespace App\Http\Controllers;

use App\DTO\SubtaskStoreDTO;
use App\DTO\TaskStoreDTO;
use App\DTO\TaskUpdateDTO;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskSearchRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskDoneResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private TaskService $taskService;

    // Constructor to inject the TaskService dependency
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    // Get a list of tasks with optional filters for status, priority, and search
    public function index(TaskSearchRequest $request): JsonResponse
    {
        $status = $request->query('status');
        $priority = $request->query('priority');
        $search = $request->query('search');

        // Retrieve tasks based on the provided filters
        $data = $this->taskService->getAllTasks($status, $priority, $search);
        return response()->json($data);
    }

    // Store a new task
    public function store(TaskRequest $request): JsonResponse
    {
        // Get validated data from the request
        $data = $request->validated();

        // Optionally, add the authenticated user ID if tasks belong to the logged-in user
        $data['user_id'] = Auth::id();

        // Create TaskStoreDTO object
        $taskDTO = new TaskStoreDTO(
            $data['title'],
            $data['description'],
            $data['priority'],
            $data['status'],
            $data['user_id']
        );

        // Create the task using the service
        $task = $this->taskService->createTask($taskDTO);

        // Return the created task in the response
        return response()->json(["data" => $task]);
    }

    // Update an existing task
    public function update(TaskUpdateRequest $request, $id): JsonResponse
    {
        // Get validated data from the request
        $data = $request->validated();

        $taskDTO = new TaskUpdateDTO(
            $data['title'],
            $data['description'],
        );

        // Update the task with the provided ID and data
        $task = $this->taskService->updateTask($id, $taskDTO);

        // Return the updated task in the response
        return response()->json(["data" => $task]);
    }

    // Mark a task as complete
    public function complete($id): TaskDoneResource|JsonResponse
    {
        // Complete the task with the provided ID
        $task = $this->taskService->completeTask($id);

        // Return the completed task in the response
        return response()->json(["data" => $task]);
    }

    // Delete a task
    public function destroy($id): JsonResponse
    {
        // Delete the task with the provided ID
        $message = $this->taskService->deleteTask($id);

        // Return a confirmation message in the response
        return response()->json(["data" => $message]);
    }

    // Create a subtask for a specific task
    public function createSubtask(TaskRequest $request, $id): JsonResponse
    {
        // Get validated data from the request
        $data = $request->validated();

        // Optionally, add the authenticated user ID if tasks belong to the logged-in user
        $data['user_id'] = Auth::id();
        $data['parent_id'] = $id;

        // Create TaskStoreDTO object
        $taskDTO = new SubtaskStoreDTO(
            $data['title'],
            $data['description'],
            $data['priority'],
            $data['status'],
            $data['user_id'],
            $data['parent_id']
        );

        // Create the subtask using the service and associate it with the parent task ID
        $task = $this->taskService->createSubtask($taskDTO, $id);

        // Return the created subtask in the response
        return response()->json(["data" => $task]);
    }
}
