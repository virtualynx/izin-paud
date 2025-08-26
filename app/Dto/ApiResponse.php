<?php

namespace App\Dto;

class ApiResponse
{
    public $status;
    public $message;
    public $data;

    public function __construct($data = null, $status = 0, $message = 'success')
    {
        $this->data = $data;
        $this->status = $status;
        $this->message = $message;
    }

}
