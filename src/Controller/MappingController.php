<?php

namespace App\Controller;

use App\Message\ProcessFileNotification;
use App\Repository\UploadsRepository;
use App\Validator\MappingValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;



class MappingController extends AbstractController
{
    private $em;
    private $repository;
    private $validator;

    public function __construct(
        UploadsRepository $repository,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->repository = $repository;
        $this->validator = new MappingValidator();
    }

    #[Route('/mapping', name: 'app_mapping_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $filesToMap = $this->repository->findBy([
            'processed' => false,
            'mapped' => false
        ]);

        if (count($filesToMap) === 0) {
            return $this->json([]);
        }

        $ids = array_map(function($file) {
            return $file->getId();
        }, $filesToMap);

        $requiredFields = $this->validator->getRequiredFields();
        $optionalFields = $this->validator->getOptionalFields();

        return $this->json([
            'unmapped_ids' => $ids,
            'required_fields' => $requiredFields,
            'optional_fields' => $optionalFields
        ]);
    }

    #[Route('/mapping/{id}', name: 'app_mapping_by_id', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getMappingById(int $id): JsonResponse
    {
        $fileToMap = $this->repository->findOneBy([
            'processed' => false,
            'mapped' => false,
            'id' => $id
        ]);

        if (!$fileToMap) {
            return $this->json([]);
        }

        $requiredFields = $this->validator->getRequiredFields();
        $optionalFields = $this->validator->getOptionalFields();

        return $this->json([
            'file_header' => explode(';', $fileToMap->getHeaders()),
            'required_fields' => $requiredFields,
            'optional_fields' => $optionalFields
        ]);
    }

    #[Route('/mapping/{id}', name: 'app_mapping_post', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function doMapping(
        int $id,
        Request $request,
        MessageBusInterface $bus
    ): JsonResponse {
        $upload =  $this->repository->findOneBy([
            'processed' => false,
            'mapped' => false,
            'id' => $id
        ]);
        if (!$upload) {
            return $this->json(['errors' => 'Not found'], 404);
        }

        $headers = explode(';', $upload->getHeaders());
        $parameters = json_decode($request->getContent(), true);

        $validate = $this->validator->validate($headers, $parameters);
        
        if (!$validate['validation']) {
            return $this->json(['errors' => $validate['errors']], 400);
        }

        $upload->setMapped(true);
        $upload->setFieldsMapping($parameters);
        
        $this->em->persist($upload);
        $this->em->flush();

        $bus->dispatch(new ProcessFileNotification($id));

        return $this->json([
            'message' => 'The file was sucessful mapped!',
            'upload_id' => $id
        ]);
    }
}
