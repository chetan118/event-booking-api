<?php

namespace App\DTO\Event;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateEventDto
{
    #[Assert\NotBlank]
    private string $title;

    #[Assert\NotBlank]
    private string $description;

    #[Assert\NotBlank]
    private string $location;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private \DateTimeInterface $startTime;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private \DateTimeInterface $endTime;

    #[Assert\NotNull]
    #[Assert\Positive]
    private int $capacity;

    public function __construct(
        string $title,
        string $description,
        string $location,
        \DateTimeInterface $startTime,
        \DateTimeInterface $endTime,
        int $capacity
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->location = $location;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->capacity = $capacity;
    }

    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getLocation(): string { return $this->location; }
    public function getStartTime(): \DateTimeInterface { return $this->startTime; }
    public function getEndTime(): \DateTimeInterface { return $this->endTime; }
    public function getCapacity(): int { return $this->capacity; }
}
