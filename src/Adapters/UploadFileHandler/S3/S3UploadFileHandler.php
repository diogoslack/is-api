<?php

namespace App\Adapters\UploadFileHandler\S3;

use App\Adapters\UploadFileHandler\UploadFileHandlerInterface;
use Aws\S3\S3Client;  
use Aws\S3\Exception\S3Exception;

class S3UploadFileHandler implements UploadFileHandlerInterface
{

    private $aws_region;
    private $aws_bucket;
    private $s3Client;

    public function __construct(
        string $aws_region,
        string $aws_bucket,
        array $aws_credentials
    ) {
        $this->aws_region = $aws_region;
        $this->aws_bucket = $aws_bucket;
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $aws_region,
            'credentials' => $aws_credentials
        ]);
    }

    public function move($file)
    {
        try {
            $fileName = 'upload' . date('YmdHis') . rand(100000,999999);

            $result = $this->s3Client->putObject([
                'Bucket' => $this->aws_bucket,
                'Key'    => $fileName,
                'ACL'    => 'private',
                'SourceFile' => $file->getPathName()
            ]);
            $result_arr = $result->toArray();

            if(!empty($result_arr['ObjectURL'])) {
                unlink($file->getPathName());
                return $result_arr['ObjectURL'];
            } else {
                return ['errors' => 'Upload Failed!'];
            }
        } catch (S3Exception $e) {
            return ['errors' => $e->getMessage()];
        }
    }

    public function remove($id)
    {

    }
}
