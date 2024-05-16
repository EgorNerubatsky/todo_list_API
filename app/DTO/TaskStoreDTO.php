<?php

namespace App\DTO;

class TaskStoreDTO
{
    public string $title;
    public string $description;
    public int $priority;
    public string $status;
    public int $user_id;

    public function __construct(string $title, string $description,int $priority,string $status,int $user_id)
    {
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->status = $status;
        $this->user_id = $user_id;
    }

}
