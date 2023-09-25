<?php

namespace App\MessageHandler;

use App\Adapters\UploadFileHandler\S3\S3UploadFileHandler;
use App\Entity\Categories;
use App\Entity\Objects;
use App\Entity\Fields;
use App\Entity\ObjectsFieldsValues;
use App\Entity\Sectors;
use App\FileReader\FileReader;
use App\FileReader\Readers\Csv;
use App\FileReader\Readers\Xlsx;
use App\Logs\FileHandlerLog;
use App\Message\ProcessFileNotification;
use App\Repository\CategoriesRepository;
use App\Repository\FieldsRepository;
use App\Repository\SectorsRepository;
use App\Repository\UploadsRepository;
use CrEOF\Spatial\PHP\Types\Geography\Point;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessFileNotificationHandler
{
    const DEFAULT_CODE = 1;
    const DEFAULT_CATEGORY_ID = 1;
    private $errors = [];

    public function __construct(
        private UploadsRepository $repository,
        private SectorsRepository $sectorsRepository,
        private CategoriesRepository $categoriesRepository,
        private FieldsRepository $fieldsRepository,
        private EntityManagerInterface $em,
        private FileReader $fileReader,
        private S3UploadFileHandler $fileHandle,
        private FileHandlerLog $log
    ) {
    }    

    public function __invoke(
        ProcessFileNotification $message
    ) {
        /* 
            NOTE: for the test sake it is not using an external transport,
            for example rabbitmq, and the queue is executed immediately.
            For a production environment, a proper transport should be defined.
            https://symfony.com/doc/current/messenger.html#transports-async-queued-messages
        */
        $uploadId = $message->getContent();

        $file = $this->repository->findOneBy([
            'processed' => false,
            'mapped' => true, 
            'id' => (int) $uploadId
        ]);
        
        $filePath = $this->fileHandle->get($file->getFile());
        $fileType = $file->getType();        

        if ($fileType == 'Xlsx') {
            $strategy = new Xlsx($filePath, $fileType);
        } else {
            $strategy = new Csv($filePath, $fileType);
        }

        $this->fileReader->setStrategy($strategy);
        $reader = $this->fileReader->getReader(null);

        /* 
            @TODO: Read the file in chunks to avoid memory issues and/or timeouts
            https://github.com/PHPOffice/PhpSpreadsheet/blob/master/samples/Reader/11_Reading_a_workbook_in_chunks_using_a_configurable_read_filter_(version_1).php
        */
        $rows = $reader->getActiveSheet()->toArray(null, true, true, true);

        $headers = explode(';', $file->getHeaders());
        $mapping = $file->getFieldsMapping();

        foreach($rows as $index => $row) {
            try {
                $isEmptyRow = $reader->getActiveSheet()->isEmptyRow($index, 3);            

                // ignore header
                if ($index == 1 || $isEmptyRow) {
                    continue;
                }

                $object = new Objects();
                $rowValues = array_values($row);
                $currentDateTime = date('Y-m-d H:i:s');
                $objectFields = [];

                foreach($headers as $key => $header) {
                    $newField = $mapping[$header];
                    $value = $rowValues[$key];
                    $originalField = $header;

                    switch ($newField) {
                        case 'object_oid':
                            $object->setOid($value);
                            break;

                        case 'object_sectorName':
                            $sector = $this->sectorsRepository->findOneByOrCreate(['name' => $value], $value);
                            $object->setSector($sector);                       
                            break;

                        case 'object_latitude':
                            $latitude = str_replace(',', '.', $value);
                            break;

                        case 'object_longitude':
                            $longitude = str_replace(',', '.', $value);
                            break;

                        case 'object_categoryName':
                            $category = $this->categoriesRepository->findOneByOrCreate(['name' => $value], $value);
                            $object->setCategory($category);                       
                            break;

                        case 'object_code':
                            $object->setCode($value);                        
                            break;

                        case 'field_value':
                            $fields = $this->fieldsRepository->findOneByOrCreate(['name' => $originalField], $value);
                            $objectField = new ObjectsFieldsValues();
                            $objectField->setFields($fields);
                            $objectField->setValue($value);
                            $objectFields[] = $objectField;                                            
                    }
                }

                $coordinates = new Point($latitude, $longitude);
                //@TODO set only a value per field
                $object->setLongitude($coordinates);
                $object->setLatitude($coordinates);

                // Apply default values for optional fields
                if (!$object->getCode()) {
                    $object->setCode(self::DEFAULT_CODE);
                }

                if (!$object->getCategory()) {
                    $category = $this->categoriesRepository->findOneBy(['id' => self::DEFAULT_CATEGORY_ID]);
                    $object->setCategory($category);
                }
                
                $object->setUpdatedAt(new DateTime($currentDateTime));
                $object->setCreatedAt(new DateTimeImmutable($currentDateTime));

                $this->em->beginTransaction();
                $this->em->persist($object);                
            
                if (count($objectFields)) {
                    foreach($objectFields as $objectField) {
                        $objectField->setObjects($object);
                        $this->em->persist($objectField);
                        $this->em->flush();
                        $object->addObjectProp($objectField);
                    }                
                }
                $this->em->flush();
                $this->em->commit();
            } catch (Exception $e) {
                $this->log->setLog('Error on line ' . $index . ': ' . $e->getMessage());
            }
        }

        // update file info on db
        $file->setErrors($this->log->getLogs());
        $file->setProcessed(true);
        $this->em->persist($file);
        $this->em->flush();
    }
}