<?php

namespace App\FileReader\Readers;

use App\FileReader\FileReaderInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class Xlsx implements FileReaderInterface
{
  private $filename;
  private $reader;
  private $totalLines = 0;

  public function __construct(string $filename, string $filetype)
  {
    $this->filename = $filename;
    $this->reader = IOFactory::createReader($filetype);
  }

  public function setTotalLines(int $total = 0)
  {
    $this->totalLines = $total;
  }

  public function getTotalLines(): int
  {
    return $this->totalLines;
  }

  public function caculateAndSetTotalLines()
  {
    $worksheetInfo = $this->reader->listWorksheetInfo($this->filename);
    $worksheet = array_shift($worksheetInfo);
    $totalLines = (int) $worksheet['totalRows'] ?? 0;
    $this->setTotalLines($totalLines);
  }

  public function fileLoader(?IReadFilter $filter = null)
  {    
    if ($filter) {
      $this->reader->setReadFilter($filter);
    }

    $this->caculateAndSetTotalLines();

    return $this->reader->load($this->filename);
  }
}
