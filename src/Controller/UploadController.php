<?php

namespace App\Controller;

use App\Adapters\UploadFileHandler\S3\S3UploadFileHandler;
use App\Entity\Uploads;
use App\FileReader\FileReader;
use App\FileReader\Readers\Csv;
use App\FileReader\Readers\Xlsx;
use App\FileReader\Filters\HeaderOnlyFilter;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadController extends AbstractController
{
    #[Route('/upload', name: 'app_upload', methods: ['POST'])]
    public function index(
        ValidatorInterface $validator,
        Request $request,
        EntityManagerInterface $em,
        S3UploadFileHandler $fileHandle,
        FileReader $fileReader
    ): JsonResponse {
        $newFile = $request->files->get('file');
        $newFileFullPath = $newFile?->getPathName() ?? '';
        $currentDateTime = date('Y-m-d H:i:s');
        $inputFileType = IOFactory::identify($newFileFullPath);        

        if ($inputFileType == 'Xlsx') {
            $strategy = new Xlsx($newFileFullPath, $inputFileType);
        } else {
            $strategy = new Csv($newFileFullPath, $inputFileType);
        }

        $fileReader->setStrategy($strategy);
        $reader = $fileReader->getReader(new HeaderOnlyFilter());

        $headers = $reader->getActiveSheet()->toArray(null, true, true, true);

        $file = new Uploads();
        $file->setFile($newFileFullPath);
        $file->setType($inputFileType);
        $file->setHeaders(implode(';', array_values($headers[1])));
        $file->setTotalLines($strategy->getTotalLines());
        $file->setProcessed(false);
        $file->setMapped(false);
        $file->setUpdatedAt(new DateTime($currentDateTime));
        $file->setCreatedAt(new DateTimeImmutable($currentDateTime));

        $errors = $validator->validate($file);

        if (count($errors) > 0) {
            foreach ($errors as $violation) {
                $errorsString[] = $violation->getMessage();
            }
            return $this->json(['errors' => $errorsString]);
        }

        try {
            $fullFilePath = $fileHandle->move($newFile);
            if (gettype($fullFilePath) == 'array') {
                return $this->json($fullFilePath);
            }
            $file->setFile($fullFilePath);

            $em->persist($file);
            $em->flush();
        } catch (Exception $e) {
            return $this->json(['errors' => $e->getMessage()]);
        }

        return $this->json([
            'message' => 'Upload finished with success!',
            'upload_id' => $file->getId()
        ]);
    }
}
