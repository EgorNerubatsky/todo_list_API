<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    // Get all tasks for the authenticated user, with optional filters for status, priority, and search
    public function getAllTasks($status, $priority, $search): Collection
    {
        $query = Task::where('user_id', auth()->id());

        // Filter tasks by status if provided
        if ($status) {
            $query->where('status', $status);
        }

        // Filter tasks by priority if provided
        if ($priority) {
            $query->where('priority', $priority);
        }

        // Filter tasks by search term if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting to the query results
        $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->orderBy('completed_at', 'asc');

        // Retrieve the filtered and sorted tasks along with their subtasks
        return $query->with('subtasks')->get();
    }

    // Save a task to the database
    public function save(Task $task): Task
    {
        $task->save();
        return $task;
    }

    // Create a new task in the database
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    // Delete a task from the database
    public function delete(Task $task): void
    {
        $task->delete();
    }

    // Find a task by its ID
    public function findTaskById(int $id): Task
    {
        return Task::findOrFail($id);
    }
}
