<?php declare(strict_types=1);

namespace Api\Account\Registry\Application;

final readonly class RegistryInput
{
  public function __construct(
    public string $name,
    public string $email,
    public string $password
  ) {
  }
}