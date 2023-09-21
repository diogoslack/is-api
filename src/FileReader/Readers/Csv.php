<?php

namespace App\FileReader\Readers;

use App\FileReader\FileReaderInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use SplFileObject;

class Csv implements FileReaderInterface
{
  private $filename;
  private $filetype;
  private $reader;
  private $totalLines = 0;

  public function __construct(string $filename, string $filetype)
  {
    $this->filename = $filename;
    $this->filetype = $filetype;
    $this->reader = IOFactory::createReader($this->filetype);
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
    $file = new SplFileObject($this->filename, 'r');
    $file->setFlags(
      SplFileObject::READ_CSV |
      SplFileObject::READ_AHEAD |
      SplFileObject::SKIP_EMPTY |
      SplFileObject::DROP_NEW_LINE
    );
    $file->seek(PHP_INT_MAX);
    $totalLines = $file->key() + 1;
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
