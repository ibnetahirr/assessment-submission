<?php

namespace App\Controller\Assessment;

use App\Domain\AssessmentAnswer;
use App\Domain\AssessmentInstance;
use App\Domain\AssessmentQuestion;
use App\Domain\AssessmentAnswerOption;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssessmentAnswerController extends AbstractController
{
    #[Route('/api/assessment/answers', methods: ['POST'])]
    public function submitAnswer(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON body'], 400);
        }

        if (!isset($data['instance_id'], $data['question_id'])) {
            return new JsonResponse(
                ['error' => 'instance_id and question_id are required'],
                400
            );
        }

        /** @var AssessmentInstance|null $instance */
        $instance = $entityManager
            ->getRepository(AssessmentInstance::class)
            ->find($data['instance_id']);

        if (!$instance) {
            return new JsonResponse(['error' => 'Assessment instance not found'], 404);
        }

        /** @var AssessmentQuestion|null $question */
        $question = $entityManager
            ->getRepository(AssessmentQuestion::class)
            ->find($data['question_id']);

        if (!$question) {
            return new JsonResponse(['error' => 'Question not found'], 404);
        }

        $session = $instance->getSession();
        if (!$session || !$session->getAssessment()) {
            return new JsonResponse(
                ['error' => 'Assessment not properly linked to instance'],
                400
            );
        }

        $assessment = $session->getAssessment();

        if ($question->getAssessment() !== $assessment) {
            return new JsonResponse(
                ['error' => 'Question does not belong to this assessment'],
                400
            );
        }

        $questionType = $question->getQuestionType();

        $answer = new AssessmentAnswer();
        $answer->setAssessmentInstance($instance);
        $answer->setAssessmentQuestion($question);
        $answer->setCreatedAt(new \DateTimeImmutable());

        if ($questionType === 'likert') {

            if (!isset($data['answer_option_id'])) {
                return new JsonResponse(
                    ['error' => 'answer_option_id is required for likert questions'],
                    400
                );
            }

            /** @var AssessmentAnswerOption|null $option */
            $option = $entityManager
                ->getRepository(AssessmentAnswerOption::class)
                ->find($data['answer_option_id']);

            if (!$option || $option->getAssessmentQuestion() !== $question) {
                return new JsonResponse(
                    ['error' => 'Invalid answer option for this question'],
                    400
                );
            }

            if (!is_numeric($option->getValue())) {
                return new JsonResponse(
                    ['error' => 'Invalid numeric value for answer option'],
                    400
                );
            }

            $answer->setAssessmentAnswerOption($option);
        }

        elseif ($questionType === 'reflection') {

            if (empty($data['text_answer'])) {
                return new JsonResponse(
                    ['error' => 'text_answer is required for reflection questions'],
                    400
                );
            }

            $answer->setTextAnswer($data['text_answer']);
        }

        else {
            return new JsonResponse(
                ['error' => 'Unsupported question type'],
                400
            );
        }

        try {
            $entityManager->persist($answer);
            $entityManager->flush();
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => 'Failed to save answer'],
                500
            );
        }

        return new JsonResponse(
            [
                'message' => 'Answer submitted successfully',
                'answer_id' => $answer->getId(),
            ],
            201
        );
    }
}