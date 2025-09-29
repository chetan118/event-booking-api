<?php

namespace App\DTO\Attendee;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateAttendeeDto
{
    #[Assert\NotBlank(allowNull: true)]
    private ?string $firstName = null;

    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastName = null;

    #[Assert\Email(allowNull: true)]
    private ?string $email = null;

    #[Assert\NotBlank(allowNull: true)]
    private ?string $country = null;

    public function __construct(?string $firstName = null, ?string $lastName = null, ?string $email = null, ?string $country = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->country = $country;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
}
