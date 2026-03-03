<?php

declare(strict_types=1);

namespace App\Domain;

class AssessmentService
{
    private AssessmentRepository $assessmentRepository;

    public function __construct(AssessmentRepository $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }

    public function getAssessmentResults(AssessmentInstance $instance): array
    {
        $answers = $this->assessmentRepository->findAllAssessmentInstanceAnswers($instance);
        $questions = $instance->getSession()->getAssessment()?->getQuestions()->toArray() ?? [];

        $progressData = $this->getProgressAndScore($instance);

        $results = [
            'instance' => [
                'id' => $instance->getId(),
                'created_at' => $instance->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updated_at' => $instance->getUpdatedAt()?->format('Y-m-d H:i:s'),
                'completed' => $instance->isCompleted(),
                'completed_at' => $instance->getCompletedAt()?->format('Y-m-d H:i:s'),
                'responder_name' => $instance->getResponderName(),
                'element' => $instance->getSession()->getAssessment()?->getElement(),
            ],
            'total_questions' => $progressData['total_questions'],
            'answered_questions' => $progressData['answered_questions'],
            'completion_percentage' => $progressData['completion_percentage'],
            'scores' => $progressData['scores'],
            'element_scores' => $progressData['element_scores'],
            'insights' => [],
        ];

        $answersByQuestion = [];
        foreach ($answers as $answer) {
            if ($answer->getAssessmentAnswerOption()) {
                $question = $answer->getAssessmentAnswerOption()->getAssessmentQuestion();
                $questionId = $question->getId();

                if (!isset($answersByQuestion[$questionId]) ||
                    $answer->getCreatedAt() > $answersByQuestion[$questionId]->getCreatedAt()) {
                    $answersByQuestion[$questionId] = $answer;
                }
            }
        }

        $results['insights'] = $this->generateInsights($instance, $answersByQuestion, $questions);

        return $results;
    }

   public function getProgressAndScore(AssessmentInstance $instance): array
{
    if (!$instance->getId()) {
        throw new \InvalidArgumentException('Invalid AssessmentInstance provided.');
    }

    $session = $instance->getSession();
    if (!$session) {
        throw new \RuntimeException('AssessmentInstance has no session.');
    }

    $assessment = $session->getAssessment();
    if (!$assessment) {
        throw new \RuntimeException('Session has no assessment assigned.');
    }

    try {
        $answers = $this->assessmentRepository
            ->findAllAssessmentInstanceAnswers($instance);
    } catch (\Throwable $e) {
        throw new \RuntimeException(
            'Failed to fetch assessment instance answers.',
            0,
            $e
        );
    }

    $questions = $assessment->getQuestions()->toArray();
    $assessmentElement = $assessment->getElement();
    $totalQuestions = count($questions);

    $answersByQuestion = [];
    foreach ($answers as $answer) {
        if ($answer->getAssessmentAnswerOption()) {
            $question = $answer->getAssessmentAnswerOption()->getAssessmentQuestion();
            $questionId = $question->getId();

            if (!isset($answersByQuestion[$questionId]) ||
                $answer->getCreatedAt() > $answersByQuestion[$questionId]->getCreatedAt()) {
                $answersByQuestion[$questionId] = $answer;
            }
        }
    }

    $totalScore = 0;
    $maxScore = 0;
    $questionAnswersData = [];
    $elementScores = [];

    $questionsByElement = [];
    foreach ($questions as $question) {
        $questionElement = $question->getElement();
        if ($questionElement) {
            $questionsByElement[$questionElement][] = $question;
        }
    }

    foreach ($questionsByElement as $element => $elementQuestions) {
        $elementTotalScore = 0;
        $elementMaxScore = 0;
        $elementAnsweredQuestions = 0;
        $elementQuestionAnswersData = [];

        foreach ($elementQuestions as $question) {
            $questionId = $question->getId();
            $answer = $answersByQuestion[$questionId] ?? null;

            try {
                $options = $this->assessmentRepository
                    ->findAssessmentAnswerOptionsByQuestion($question);
            } catch (\Throwable $e) {
                throw new \RuntimeException(
                    "Failed to fetch answer options for question {$questionId}.",
                    0,
                    $e
                );
            }

            $questionMaxScore = 0;

            if (!empty($options)) {
                $values = array_map(fn($option) => $option->getValue(), $options);
                $numericValues = array_filter($values, fn($v) => is_numeric($v));

                if (empty($numericValues)) {
                    throw new \RuntimeException(
                        "Invalid or missing numeric option values for question {$questionId}."
                    );
                }

                $questionMaxScore = max($numericValues);
            }

            $questionData = [
                'question_id' => $questionId,
                'question_title' => $question->getTitle(),
                'question_suite' => $question->getQuestionSuite(),
                'question_sequence' => $question->getSequence(),
                'is_reflection' => $question->getIsReflection(),
                'reflection_prompt' => $question->getReflectionPrompt(),
                'element' => $question->getElement(),
                'max_score' => $questionMaxScore,
                'is_answered' => $answer !== null,
                'answer_id' => $answer?->getId(),
                'answer_value' => null,
                'answer_text' => null,
                'answer_option_id' => null,
                'text_answer' => $answer?->getTextAnswer(),
                'numeric_value' => $answer?->getNumericValue(),
            ];

            if ($answer && $answer->getAssessmentAnswerOption()) {
                $answerOption = $answer->getAssessmentAnswerOption();

                if (!is_numeric($answerOption->getValue())) {
                    throw new \RuntimeException(
                        "Invalid numeric value for selected answer option in question {$questionId}."
                    );
                }

                $value = (float) $answerOption->getValue();

                $questionData['answer_value'] = $value;
                $questionData['answer_text'] = $answerOption->getAnswer();
                $questionData['answer_option_id'] = $answerOption->getId();
                $questionData['answer_explanation'] = $answerOption->getExplanation();
                $questionData['option_number'] = $answerOption->getOptionNumber();

                $elementTotalScore += $value;
                $totalScore += $value;

                $elementMaxScore += $questionMaxScore;
                $maxScore += $questionMaxScore;

                $elementAnsweredQuestions++;
            }

            $elementQuestionAnswersData[] = $questionData;
            $questionAnswersData[] = $questionData;
        }

        $elementCompletionPercentage = count($elementQuestions) > 0
            ? round(($elementAnsweredQuestions / count($elementQuestions)) * 100, 2)
            : 0;

        $normalizedElementScore = max(0, $elementTotalScore - $elementAnsweredQuestions);
        $normalizedElementMaxScore = max(0, $elementMaxScore - $elementAnsweredQuestions);

        $elementScorePercentage = 0;
        if ($normalizedElementMaxScore > 0) {
            $elementScorePercentage = round(
                ($normalizedElementScore / $normalizedElementMaxScore) * 100,
                2
            );
        }

        $elementScorePercentage = max(0, min(100, $elementScorePercentage));

        $elementScores[$element] = [
            'element' => $element,
            'total_questions' => count($elementQuestions),
            'answered_questions' => $elementAnsweredQuestions,
            'completion_percentage' => min($elementCompletionPercentage, 100),
            'scores' => [
                'total_score' => $elementTotalScore,
                'max_score' => $elementMaxScore,
                'percentage' => $elementScorePercentage,
            ],
            'question_answers' => $elementQuestionAnswersData,
        ];
    }

    usort($questionAnswersData, fn($a, $b) =>
        $a['question_sequence'] <=> $b['question_sequence']
    );

    $answeredQuestions = count($answersByQuestion);

    $completionPercentage = $totalQuestions > 0
        ? round(($answeredQuestions / $totalQuestions) * 100, 2)
        : 0;

    $normalizedTotalScore = max(0, $totalScore - $answeredQuestions);
    $normalizedTotalMaxScore = max(0, $maxScore - $answeredQuestions);

    $scorePercentage = 0;
    if ($normalizedTotalMaxScore > 0) {
        $scorePercentage = round(
            ($normalizedTotalScore / $normalizedTotalMaxScore) * 100,
            2
        );
    }

    $scorePercentage = max(0, min(100, $scorePercentage));

    return [
        'total_questions' => $totalQuestions,
        'answered_questions' => $answeredQuestions,
        'completion_percentage' => min($completionPercentage, 100),
        'scores' => [
            'element' => $assessmentElement,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $scorePercentage,
        ],
        'question_answers' => $questionAnswersData,
        'element_scores' => $elementScores,
    ];
}

    private function generateInsights(AssessmentInstance $instance, array $answersByQuestion, array $questions): array
    {
        $insights = [];
        $element = $instance->getSession()->getAssessment()->getElement();

        // Basic completion insight
        $completionRate = count($answersByQuestion) / max(count($questions), 1);
        if ($completionRate >= 1.0) {
            $insights[] = [
                'type' => 'completion',
                'message' => "You have completed your self-assessment for element {$element}.",
                'positive' => true,
            ];
        } else {
            $remaining = count($questions) - count($answersByQuestion);
            $insights[] = [
                'type' => 'completion',
                'message' => "You have {$remaining} questions remaining to complete this assessment.",
                'positive' => false,
            ];
        }

        $scores = [];
        foreach ($answersByQuestion as $questionId => $answer) {
            if ($answer->getAssessmentAnswerOption()) {
                $scores[] = $answer->getAssessmentAnswerOption()->getValue();
            }
        }

        if (!empty($scores)) {
            $averageScore = array_sum($scores) / count($scores);

            if ($averageScore >= 4) {
                $insights[] = [
                    'type' => 'performance',
                    'message' => "You demonstrate strong confidence in this element of teaching practice.",
                    'positive' => true,
                ];
            } elseif ($averageScore >= 3) {
                $insights[] = [
                    'type' => 'performance',
                    'message' => "You show good understanding with some areas for development.",
                    'positive' => true,
                ];
            } else {
                $insights[] = [
                    'type' => 'performance',
                    'message' => "This element may benefit from further reflection and development.",
                    'positive' => false,
                ];
            }
        }

        return $insights;
    }
}
