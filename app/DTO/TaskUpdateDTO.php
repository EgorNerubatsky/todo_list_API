<?php

namespace App\DTO;

class TaskUpdateDTO
{
    public string $title;
    public string $description;


    public function __construct(string $title, string $description)
    {
        $this->title = $title;
        $this->description = $description;

    }

}
