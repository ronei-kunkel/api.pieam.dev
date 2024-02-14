<?php declare(strict_types=1);

namespace Api\_Common\Infra\Repository;

use Api\_Common\Domain\Entity\Session;
use SessionHandlerInterface;

final class SessionRepository
{
  private SessionHandlerInterface $handler;

  public function __construct(
  ) {
    $this->handler = session()->getHandler();
  }

  public function save(Session $session): ?string
  {
    $sessionId = sha1((string) (time() + rand()));

    if (!$this->handler->write($sessionId, serialize($session))) {
      return null;
    }
    return $sessionId;
  }

  public function destroy(string $sessionId): bool
  {
    if (!$this->handler->destroy($sessionId)) {
      return false;
    }

    return true;
  }

  // public function put(string $sessionId, string $key, string $value): void
  // {
  //   $session = $this->handler->read($sessionId);

  //   $sessionData       = json_decode($session);
  //   $sessionData->$key = $value;

  //   $this->handler->write($sessionId, json_encode($sessionData));
  // }

  public function has(string $sessionId): bool
  {
    if (!$this->handler->read($sessionId)) {
      return false;
    }

    return true;
  }


  public function get(string $sessionId): ?Session
  {
    try {
      $session = unserialize($this->handler->read($sessionId));

      if (!$session) {
        return null;
      }

      return $session;

    } catch (\Throwable $th) {
      return null;
    }
  }
}
