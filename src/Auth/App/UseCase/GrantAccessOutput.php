<?php declare(strict_types=1);

namespace Api\Auth\App\UseCase;

use Api\_Common\Domain\Entity\User;

final readonly class GrantAccessOutput
{
  public function __construct(
    public bool $success,
    public ?User $user,
    public array $errors
  ) {
  }
}
