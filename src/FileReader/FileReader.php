<?php

namespace App\FileReader;

use App\FileReader\FileReaderInterface;

class FileReader
{
  private $strategy;

  public function setStrategy($strategy)
  {
    $this->strategy = $strategy;
  }

  public function getReader($filter)
  {
    return $this->strategy->fileLoader($filter);
  }

}
