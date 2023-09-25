<?php

namespace App\Logs;

use App\Logs\LogInterface;

class FileHandlerLog implements LogInterface
{
  private $errors = [];

  public function setLog(string $message): void
    {
        $this->errors[] = $message;
    }

  public function getLogs(): array
  {
      return $this->errors;
  }
}
