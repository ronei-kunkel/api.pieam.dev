<?php declare(strict_types=1);

namespace Api\Account\Registry\Domain;

final class Email
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
    if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $this->violations[] = 'Email must have a valid format';
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
