<?php

namespace App\Adapters\UploadFileHandler;

interface UploadFileHandlerInterface
{
  public function move($file);
  public function remove($id);
}
