<?php

namespace App\DTO;

class SubtaskStoreDTO
{
    public string $title;
    public string $description;
    public int $priority;
    public string $status;
    public int $user_id;
    public int $parent_id;

    public function __construct(
        string $title,
        string $description,
        int $priority,
        string $status,
        int $user_id,
        int $parent_id
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->status = $status;
        $this->user_id = $user_id;
        $this->parent_id = $parent_id;
    }

}
