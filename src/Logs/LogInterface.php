<?php

namespace App\Logs;

interface LogInterface
{
  public function setLog(string $message);
  public function getLogs();
}
