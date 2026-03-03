<?php

namespace App\Controller\Assessment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssessmentAnswerController extends AbstractController
{
    #[Route('/api/assessment/answers', methods: ['POST'])]
    public function submitAnswer(Request $request): JsonResponse
    {
        return new JsonResponse(['message' => 'Route works'], 201);
    }
}