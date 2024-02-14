<?php declare(strict_types=1);

namespace Api\_Common\Domain\Entity;

final class Session
{

  public function __construct(
    private User $user
  ) {
  }

  public function user(): User
  {
    return $this->user;
  }
}
