<?php declare(strict_types=1);

namespace Api\Auth\App\UseCase;

final readonly class GrantAccessInput
{
  public function __construct(
    public string $firstName,
    public string $lastName,
    public string $email,
    public string $googleId,
    public string $imageUrl
  ){
  }
}