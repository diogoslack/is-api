<?php

namespace App\FileReader;

use App\FileReader\FileReaderInterface;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FileReader
{
  private $strategy;

  public function setStrategy(FileReaderInterface $strategy)
  {
    $this->strategy = $strategy;
  }

  public function getReader(?IReadFilter $filter): Spreadsheet
  {
    return $this->strategy->fileLoader($filter);
  }

}
