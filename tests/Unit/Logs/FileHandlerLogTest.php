<?php

namespace App\Tests\Unit\Logs;

use App\Logs\FileHandlerLog;
use PHPUnit\Framework\TestCase;

class FileHandlerLogTest extends TestCase 
{
  public function testSetAndGetLogs(): void
  {
    $testContent = ['test log1', 'test log2'];
    $log = new FileHandlerLog();
    $log->setLog($testContent[0]);
    $log->setLog($testContent[1]);
    self::assertSame($testContent, $log->getLogs());
  }

  public function testGetLogsWithNoData(): void
  {
    $log = new FileHandlerLog();
    self::assertSame([], $log->getLogs());
  } 
}
