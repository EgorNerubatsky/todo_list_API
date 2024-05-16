<?php

namespace App\Services;

use App\DTO\SubtaskStoreDTO;
use App\DTO\TaskStoreDTO;
use App\DTO\TaskUpdateDTO;
use App\Enums\TaskStatus;
use App\Http\Resources\SubtaskResource;
use App\Http\Resources\TaskDoneResource;
use App\Http\Resources\TaskResource;
use App\Repositories\TaskRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

class TaskService
{
    private TaskRepository $taskRepository;

    // Constructor to inject the TaskRepository dependency
    public function __construct(
        TaskRepository $taskRepository,
    )
    {
        $this->taskRepository = $taskRepository;
    }

    // Retrieve all tasks with optional filters for status, priority, and search
    public function getAllTasks($status, $priority, $search): array|AnonymousResourceCollection
    {
        try {
            $tasks = $this->getTasksTree($status, $priority, $search);
            return TaskResource::collection($tasks);
        } catch (Exception $e) {
            // If an error occurs during the operation, return a 500 Internal Server Error response
            return (['message' => $e->getMessage()]);
        }
    }

    // Get tasks organized in a tree structure
    public function getTasksTree($status, $priority, $search): Collection
    {
        $tasks = $this->taskRepository->getAllTasks($status, $priority, $search);
        $groupedTasks = $tasks->groupBy('parent_id');

        return $this->buildTree($groupedTasks);
    }

    // Recursively build a tree of tasks and subtasks
    protected function buildTree($groupedTasks, $parentId = null): Collection
    {
        if (!$groupedTasks->has($parentId)) {
            return collect([]);
        }

        return $groupedTasks[$parentId]->map(function ($task) use ($groupedTasks) {
            $task->subtasks = $this->buildTree($groupedTasks, $task->id);
            return $task;
        });
    }

    // Create a new task
    public function createTask(TaskStoreDTO $taskDTO): TaskResource
    {
        $taskData = [
            'title' => $taskDTO->title,
            'description' => $taskDTO->description,
            'priority' => $taskDTO->priority,
            'status' => $taskDTO->status,
            'user_id' => $taskDTO->user_id,
        ];

        // Create task using taskData
        $task = $this->taskRepository->create($taskData);

        return TaskResource::make($task);
    }

    // Create a subtask for a specific task
    public function createSubtask(SubtaskStoreDTO $taskDTO, $id): array|SubtaskResource
    {
        try {
            $task = $this->taskRepository->findTaskById($id);

            if ($task->user_id === auth()->id()) {
                $taskData = [
                    'title' => $taskDTO->title,
                    'description' => $taskDTO->description,
                    'priority' => $taskDTO->priority,
                    'status' => $taskDTO->status,
                    'user_id' => $taskDTO->user_id,
                    'parent_id' => $taskDTO->parent_id,
                ];

                // Create the subtask
                $task = $this->taskRepository->create($taskData);

                return SubtaskResource::make($task);
            } else {
                // Return a 403 Forbidden response if the user is not the owner of the task
                return (['message' => "You are not allowed to update someone else's task"]);
            }
        } catch (ModelNotFoundException $e) {
            // If the task does not exist, return a 404 Not Found response
            return (['message' => "Task not found"]);
        } catch (Exception $e) {
            // If an error occurs during the operation, return a 500 Internal Server Error response
            return (['message' => $e->getMessage()]);
        }
    }

    // Update an existing task
    public function updateTask($id, TaskUpdateDTO $taskDTO): array|TaskResource|JsonResponse
    {
        try {
            $task = $this->taskRepository->findTaskById($id);

            if ($task->user_id === auth()->id()) {

                // Fill the task with the provided data
                $taskData = [
                    'title' => $taskDTO->title,
                    'description' => $taskDTO->description,
                ];
                $task->fill($taskData);

                // Save the updated task
                $task = $this->taskRepository->save($task);

                return TaskResource::make($task);
            } else {
                // Return a 403 Forbidden response if the user is not the owner of the task
                return (['message' => "You are not allowed to update someone else's task"]);
            }
        } catch (ModelNotFoundException $e) {
            // If the task does not exist, return a 404 Not Found response
            return (['message' => "Task not found"]);
        } catch (Exception $e) {
            // If an error occurs during the operation, return a 500 Internal Server Error response
            return (['message' => $e->getMessage()]);
        }
    }

    // Delete a task
    public function deleteTask($id): array
    {
        try {
            $task = $this->taskRepository->findTaskById($id);

            if ($task->user_id === auth()->id()) {
                if($task->status == "done"){
                    // Return a message indicating that completed tasks cannot be deleted
                    return (['message' => "It is impossible to delete a task that has already been completed"]);
                }
                $this->taskRepository->delete($task);
                return (['message' => "Task deleted"]);
            } else {
                // Return a 403 Forbidden response if the user is not the owner of the task
                return (['message' => "You are not allowed to delete someone else's task"]);
            }
        } catch (ModelNotFoundException) {
            // If the task does not exist, return a 404 Not Found response
            return (['message' => "Task not found"]);
        } catch (Exception $e) {
            // If an error occurs during the operation, return a 500 Internal Server Error response
            return (['message' => $e->getMessage()]);
        }
    }

    // Mark a task as complete
    public function completeTask($id): array|TaskDoneResource|JsonResponse
    {
        try {
            $task = $this->taskRepository->findTaskById($id);
            $subtasks = $task->subtasks()->pluck('status')->toArray();

            if ($task->user_id === auth()->id()) {
                if(in_array(TaskStatus::todo(), $subtasks)){
                    // Return a message indicating that not all subtasks are completed
                    return (['message' => "Not all subtasks have been completed"]);
                }
                // Mark the task as completed
                $task->completed_at = now();
                $task->status = TaskStatus::done();
                $task = $this->taskRepository->save($task);

                return TaskDoneResource::make($task);
            } else {
                // Return a 403 Forbidden response if the user is not the owner of the task
                return (['message' => "You are not allowed to update someone else's task"]);
            }
        } catch (ModelNotFoundException $e) {
            // If the task does not exist, return a 404 Not Found response
            return response()->json(['message' => "Task not found"], 404);
        } catch (Exception $e) {
            // If an error occurs during the operation, return a 500 Internal Server Error response
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
