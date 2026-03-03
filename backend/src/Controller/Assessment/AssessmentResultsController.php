<?php

declare(strict_types=1);

namespace App\Controller\Assessment;

use App\Domain\AssessmentRepository;
use App\Domain\AssessmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssessmentResultsController extends AbstractController
{
    private AssessmentRepository $assessmentRepository;
    private AssessmentService $assessmentService;

    public function __construct(
        AssessmentRepository $assessmentRepository,
        AssessmentService $assessmentService
    ) {
        $this->assessmentRepository = $assessmentRepository;
        $this->assessmentService = $assessmentService;
    }

    /**
     * @Route("/api/assessment/results/{id}", methods={"GET"})
     */
    public function __invoke(string $id): JsonResponse
    {
        $instance = $this->assessmentRepository->findAssessmentInstanceById($id);

        if (!$instance) {
            return $this->json([
                'error' => 'Assessment instance not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $results = $this->assessmentService->getAssessmentResults($instance);

        return $this->json($results);
    }
}
