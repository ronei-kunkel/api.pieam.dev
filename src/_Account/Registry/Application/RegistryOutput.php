<?php declare(strict_types=1);

namespace Api\Account\Registry\Application;

final readonly class RegistryOutput
{
  public function __construct(
    public bool $status,
    public array $errors,
    public int $code
  ) {
  }
}
