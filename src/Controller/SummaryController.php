<?php

namespace App\Controller;

use App\Repository\UploadsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;



class SummaryController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(
        UploadsRepository $repository,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->repository = $repository;
    }

    #[Route('/summary/{id}', name: 'app_summary_by_id', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getSummaryById(int $id): JsonResponse
    {
        $summary = $this->repository->findOneBy([
            'id' => $id
        ]);

        if (!$summary) {
            return $this->json(['errors' => 'Not found'], 404);
        }

        return $this->json([
            'is_processed' => $summary->isProcessed(),
            'is_mapped' => $summary->isMapped(),
            'total_lines' => $summary->getTotalLines(),
            'errors' => $summary->getErrors(),
            'type' => $summary->getType(),
            'last_update' => $summary->getUpdatedAt()
        ]);
    }

}
