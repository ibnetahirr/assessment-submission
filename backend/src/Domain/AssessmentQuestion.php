<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="assessment_questions")
 * @ORM\Entity
 */
class AssessmentQuestion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\Column(name="title", type="text")
     */
    private string $title;

    /**
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    private ?int $sequence = null;

    /**
     * @ORM\Column(name="question_type", type="string", length=50, options={"default": "likert"})
     */
    private string $questionType = 'likert';

    /**
     * @ORM\Column(name="is_reflection", type="boolean", nullable=true)
     */
    private ?bool $isReflection = null;

    /**
     * @ORM\Column(name="reflection_prompt", type="text", nullable=true)
     */
    private ?string $reflectionPrompt = null;

    /**
     * @ORM\Column(name="responder_types", type="json", nullable=true)
     */
    private ?array $responderTypes = null;

    /**
     * @ORM\Column(name="element", type="string", length=255, nullable=true)
     */
    private ?string $element;

    /**
     * @ORM\Column(name="question_suite", type="string", length=255, nullable=true)
     */
    private ?string $questionSuite = null;

    /**
     * @ORM\ManyToMany(targetEntity=Assessment::class, mappedBy="questions", fetch="EXTRA_LAZY")
     */
    private Collection $assessments;

    /**
     * @ORM\OneToMany(targetEntity=AssessmentAnswerOption::class, mappedBy="assessmentQuestion")
     * @ORM\OrderBy({"optionNumber" = "ASC"})
     */
    private Collection $answerOptions;

    public function __construct(
        ?string $id,
        string $title,
        ?int $sequence,
        string $questionType,
        ?bool $isReflection,
        ?string $reflectionPrompt,
        ?array $responderTypes,
        ?string $element,
        ?string $questionSuite = null,
    ) {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->title = $title;
        $this->sequence = $sequence;
        $this->questionType = $questionType;
        $this->isReflection = $isReflection;
        $this->reflectionPrompt = $reflectionPrompt;
        $this->responderTypes = $responderTypes ?? ['self'];
        $this->assessments = new ArrayCollection();
        $this->answerOptions = new ArrayCollection();
        $this->element = $element;
        $this->questionSuite = $questionSuite;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(?int $sequence): void
    {
        $this->sequence = $sequence;
    }

    public function getQuestionType(): string
    {
        return $this->questionType;
    }

    public function setQuestionType(string $questionType): void
    {
        $this->questionType = $questionType;
    }

    public function getIsReflection(): ?bool
    {
        return $this->isReflection;
    }

    public function setIsReflection(?bool $isReflection): void
    {
        $this->isReflection = $isReflection;
    }

    public function getReflectionPrompt(): ?string
    {
        return $this->reflectionPrompt;
    }

    public function setReflectionPrompt(?string $reflectionPrompt): void
    {
        $this->reflectionPrompt = $reflectionPrompt;
    }

    public function getResponderTypes(): ?array
    {
        return $this->responderTypes;
    }

    public function setResponderTypes(?array $responderTypes): void
    {
        $this->responderTypes = $responderTypes;
    }

    public function getElement(): ?string
    {
        return $this->element;
    }

    public function setElement(?string $element): void
    {
        $this->element = $element;
    }

    public function getQuestionSuite(): ?string
    {
        return $this->questionSuite;
    }

    public function setQuestionSuite(?string $questionSuite): void
    {
        $this->questionSuite = $questionSuite;
    }

    public function getAssessments(): Collection
    {
        return $this->assessments;
    }

    public function getAnswerOptions(): Collection
    {
        return $this->answerOptions;
    }
}
