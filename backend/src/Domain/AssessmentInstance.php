<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="assessment_instance")
 * @ORM\Entity
 */
class AssessmentInstance
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="guid")
     */
    private string $id;

    /**
     * @ORM\Column(name="responder_name", type="string", length=255, nullable=true)
     */
    private ?string $responderName = null;

    /** @ORM\Column(name="completed_at", type="datetime", nullable=true) */
    private ?\DateTime $completedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=AssessmentSession::class, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=true )
     */
    private ?AssessmentSession $session;

    public function __construct(
        ?string $id,
        AssessmentSession $session,
        ?string $responderName = null
    ) {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->session = $session;
        $this->responderName = $responderName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getResponderName(): ?string
    {
        return $this->responderName;
    }

    public function setResponderName(?string $responderName): void
    {
        $this->responderName = $responderName;
    }

    public function setCompleted(): void
    {
        $this->completedAt = new \DateTime();
    }

    public function isCompleted(): bool
    {
        return null !== $this->completedAt;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function getSession(): ?AssessmentSession
    {
        return $this->session;
    }

    public function setSession(?AssessmentSession $session): void
    {
        $oldSession = $this->session;

        if ($oldSession !== null && $oldSession->getInstances()->contains($this)) {
            $oldSession->getInstances()->removeElement($this);
        }

        $this->session = $session;

        if ($session !== null && !$session->getInstances()->contains($this)) {
            $session->getInstances()->add($this);
        }
    }
}
