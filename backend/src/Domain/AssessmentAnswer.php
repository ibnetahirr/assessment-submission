<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="assessment_answers")
 * @ORM\Entity
 */
class AssessmentAnswer
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=AssessmentInstance::class, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="assessment_instance_id", referencedColumnName="id")
     */
    private AssessmentInstance $assessmentInstance;

    /**
     * @ORM\ManyToOne(targetEntity=AssessmentAnswerOption::class, fetch="EXTRA_LAZY", cascade={"PERSIST"})
     * @ORM\JoinColumn(name="assessment_answer_option_id", referencedColumnName="id", nullable=true)
     */
    private ?AssessmentAnswerOption $assessmentAnswerOption = null;

    /**
     * @ORM\Column(name="text_answer", type="text", nullable=true)
     */
    private ?string $textAnswer = null;

    /**
     * @ORM\Column(name="numeric_value", type="integer", nullable=true)
     */
    private ?int $numericValue = null;

    public function __construct(
        ?string $id,
        AssessmentInstance $assessmentInstance,
        ?AssessmentAnswerOption $assessmentAnswerOption = null,
        ?string $textAnswer = null,
        ?int $numericValue = null
    ) {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->assessmentInstance = $assessmentInstance;
        $this->assessmentAnswerOption = $assessmentAnswerOption;
        $this->textAnswer = $textAnswer;
        $this->numericValue = $numericValue;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAssessmentInstance(): AssessmentInstance
    {
        return $this->assessmentInstance;
    }

    public function getAssessmentAnswerOption(): ?AssessmentAnswerOption
    {
        return $this->assessmentAnswerOption;
    }

    public function setAssessmentInstance(AssessmentInstance $assessmentInstance): void
    {
        $this->assessmentInstance = $assessmentInstance;
    }

    public function setAssessmentAnswerOption(?AssessmentAnswerOption $assessmentAnswerOption): void
    {
        $this->assessmentAnswerOption = $assessmentAnswerOption;
    }

    public function getTextAnswer(): ?string
    {
        return $this->textAnswer;
    }

    public function setTextAnswer(?string $textAnswer): void
    {
        $this->textAnswer = $textAnswer;
    }

    public function getNumericValue(): ?int
    {
        return $this->numericValue;
    }

    public function setNumericValue(?int $numericValue): void
    {
        $this->numericValue = $numericValue;
    }

    public function getAnswerValue(): mixed
    {
        if ($this->assessmentAnswerOption) {
            return $this->assessmentAnswerOption->getValue();
        }

        if ($this->numericValue !== null) {
            return $this->numericValue;
        }

        return $this->textAnswer;
    }
}
