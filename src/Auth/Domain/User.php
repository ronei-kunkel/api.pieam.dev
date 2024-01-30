<?php declare(strict_types=1);

namespace Api\Auth\Domain;

final class User
{
  private ?int $id;

  public function __construct(
    private string $name,
    private string $lastName,
    private string $email,
    private string $googleId,
    private string $imageUrl
  ){
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getLastName(): string
  {
    return $this->lastName;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getGoogleId(): string
  {
    return $this->googleId;
  }

  public function getImageUrl(): string
  {
    return $this->imageUrl;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): self
  {
    $this->id = $id;
    return $this;
  }
}
