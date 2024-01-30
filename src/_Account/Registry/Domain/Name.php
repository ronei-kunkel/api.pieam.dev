<?php declare(strict_types=1);

namespace Api\Account\Registry\Domain;

final class Name
{
  private array $violations = [];
  private false|string $value;

  public function __construct(
    string $value
  ) {
    $this->validateValue($value);

    if($this->hasViolations()) {
      $this->value = false;
      return;
    }

    $this->value = $value;
  }

  private function validateValue($value): void
  {
    if (strlen($value) < 2) {
      $this->violations[] = 'Name must have at least 2 characteres';
    }
  }

  public function hasViolations(): bool
  {
    return !empty($this->violations);
  }

  public function getViolations(): array
  {
    return $this->violations;
  }

  public function getValue(): string|false
  {
    return $this->value;
  }
}
