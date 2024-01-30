<?php declare(strict_types=1);

namespace Api\Auth\App;

use Api\Auth\Domain\User;

final readonly class GrantAccessOutput
{
  public function __construct(
    public bool $success,
    public ?User $user,
    public array $errors
  ) {
  }
}
