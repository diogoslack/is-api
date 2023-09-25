<?php

namespace App\Tests\Unit\Message;

use App\Message\ProcessFileNotification;
use PHPUnit\Framework\TestCase;

class ProcessFileNotificationTest extends TestCase 
{
  public function testNotification(): void
  {
    $message = 'My Test Message';
    $notification = new ProcessFileNotification($message);    
    self::assertSame($message, $notification->getContent());
  }
}
