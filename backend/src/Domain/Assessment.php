<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="assessment")
 * @ORM\Entity(repositoryClass="App\Domain\AssessmentRepository")
 */
class Assessment
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\Column(name="element", type="string", length=255)
     */
    private string $element;

    /**
     * @ORM\Column(name="version", type="string", length=50, nullable=true)
     */
    private ?string $version;

    /**
     * @ORM\Column(name="active", type="boolean", options={"default": true})
     */
    private bool $active = true;

    /**
     * @ORM\Column(name="type", type="string", length=20, options={"default": "self"})
     */
    private string $type = 'self';

    /**
     * @ORM\ManyToMany(targetEntity=AssessmentQuestion::class,inversedBy="assessments", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="assessments_questions",
     *      joinColumns={@ORM\JoinColumn(name="assessment_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="assessmentquestion_id", referencedColumnName="id")}
     * )
     */
    private Collection $questions;

    public function __construct(
        ?string $id,
        string $element,
        ?string $version = null,
        bool $active = true,
        string $type = 'self'
    ) {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->element = $element;
        $this->version = $version;
        $this->active = $active;
        $this->setType($type);
        $this->questions = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getElement(): string
    {
        return $this->element;
    }

    public function setElement(string $element): void
    {
        $this->element = $element;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $validTypes = ['self', 'peer', 'student'];

        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException('Invalid responder type: ' . $type);
        }

        $this->type = $type;
    }

    public function isSelfAssessment(): bool
    {
        return $this->type === 'self';
    }

    public function isPeerAssessment(): bool
    {
        return $this->type === 'peer';
    }

    public function isStudentAssessment(): bool
    {
        return $this->type === 'student';
    }

    public function getQuestions(): ?Collection
    {
        return $this->questions;
    }

    public function setQuestions(?Collection $questions): void
    {
        $this->questions = $questions;
    }

    public function addQuestion(AssessmentQuestion $question): void
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->getAssessments()->add($this);
        }
    }

    public function removeQuestion(AssessmentQuestion $question): void
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
        }
    }
}
