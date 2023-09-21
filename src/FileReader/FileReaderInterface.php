<?php

namespace App\FileReader;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

interface FileReaderInterface
{
  public function setTotalLines(int $total = 0);
  public function getTotalLines();
  public function caculateAndSetTotalLines();
  public function fileLoader(?IReadFilter $filter = null);
}
