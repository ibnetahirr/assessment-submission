<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="assessment_answer_options")
 * @ORM\Entity
 */
class AssessmentAnswerOption
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\Column(name="answer", type="text")
     */
    private string $answer;

    /**
     * @ORM\Column(name="value", type="integer")
     */
    private int $value;

    /**
     * @ORM\Column(name="option_number", type="integer", nullable=true)
     */
    private ?int $optionNumber;

    /**
     * @ORM\Column(name="explanation", type="text", nullable=true)
     */
    private ?string $explanation;

    /**
     * @ORM\ManyToOne(targetEntity=AssessmentQuestion::class, fetch="EXTRA_LAZY", cascade={"PERSIST"})
     * @ORM\JoinColumn(name="assessment_question_id", referencedColumnName="id")
     */
    private AssessmentQuestion $assessmentQuestion;

    public function __construct(
        ?string $id,
        string $answer,
        int $value,
        ?int $optionNumber,
        ?string $explanation,
        AssessmentQuestion $assessmentQuestion
    ) {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->answer = $answer;
        $this->value = $value;
        $this->optionNumber = $optionNumber ?? 1;
        $this->explanation = $explanation;
        $this->assessmentQuestion = $assessmentQuestion;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getOptionNumber(): ?int
    {
        return $this->optionNumber ?? null;
    }

    public function setOptionNumber(int $optionNumber): void
    {
        $this->optionNumber = $optionNumber;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): void
    {
        $this->explanation = $explanation;
    }

    public function getAssessmentQuestion(): AssessmentQuestion
    {
        return $this->assessmentQuestion;
    }

    public function setAssessmentQuestion(AssessmentQuestion $assessmentQuestion): void
    {
        $this->assessmentQuestion = $assessmentQuestion;
    }
}
