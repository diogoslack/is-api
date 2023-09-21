<?php

namespace App\FileReader\Filters;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class HeaderOnlyFilter implements IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = '') {
        if ($row === 1) {
            return true;
        }
        return false;
    }
}
