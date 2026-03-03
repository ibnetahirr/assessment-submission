<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="assessment_session")
 * @ORM\Entity
 */
class AssessmentSession
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\Column(name="user_name", type="string", length=255, nullable=true)
     */
    private ?string $userName = null;

    /**
     * @ORM\ManyToOne(targetEntity=Assessment::class, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="assessment_id", referencedColumnName="id")
     */
    private Assessment $assessment;

    /**
     * @ORM\Column(name="responder_type", type="string", length=50, options={"default": "self"})
     */
    private string $responderType = 'self';

    /**
     * @ORM\Column(name="expected_responses", type="integer", nullable=true )
     */
    private ?int $expectedResponses;

    /**
     * @ORM\OneToMany(targetEntity=AssessmentInstance::class, mappedBy="session", fetch="EXTRA_LAZY")
     */
    private ?Collection $instances;

    /**
     * @ORM\Column(name="invitation_token", type="string", length=255, nullable=true)
     */
    private ?string $invitationToken = null;

    public function __construct(
        ?string $id,
        Assessment $assessment,
        ?string $userName = null,
        ?int $expectedResponses = 1,
        string $responderType = 'self',
        ?string $invitationToken = null,
    ) {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->userName = $userName;
        $this->expectedResponses = $expectedResponses;
        $this->responderType = $responderType;
        $this->assessment = $assessment;
        $this->invitationToken = $invitationToken;
        $this->instances = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    public function getExpectedResponses(): ?int
    {
        return $this->expectedResponses;
    }

    public function setExpectedResponses(?int $expectedResponses): void
    {
        $this->expectedResponses = $expectedResponses;
    }

    public function getInstances(): ?Collection
    {
        return $this->instances;
    }

    public function getResponderType(): string
    {
        return $this->responderType;
    }

    public function setResponderType(string $responderType): void
    {
        $this->responderType = $responderType;
    }

    public function getAssessment(): Assessment
    {
        return $this->assessment;
    }

    public function setAssessment(Assessment $assessment): void
    {
        $this->assessment = $assessment;
    }

    public function getInvitationToken(): ?string
    {
        return $this->invitationToken;
    }

    public function setInvitationToken(?string $invitationToken): void
    {
        $this->invitationToken = $invitationToken;
    }

    public function addInstance(AssessmentInstance $instance): self
    {
        if (!$this->instances->contains($instance)) {
            $this->instances->add($instance);

            if ($instance->getSession() !== $this) {
                $instance->setSession($this);
            }
        }

        return $this;
    }

    public function removeInstance(AssessmentInstance $instance): self
    {
        if ($this->instances->contains($instance)) {
            $this->instances->removeElement($instance);

            if ($instance->getSession() === $this) {
                $instance->setSession(null);
            }
        }

        return $this;
    }
}
