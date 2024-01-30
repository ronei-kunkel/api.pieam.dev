<?php declare(strict_types=1);

namespace Api\Account\Registry\Domain;

/**
 * Password must have at least:
 * - 12 chars
 * - one special char between !@$%#&
 * - one letter in uppercase
 * - one letter in lowercase
 */
final class Password
{
  private array $violations = [];
  private string|false $value;
  private string $algo = PASSWORD_BCRYPT;

  public function __construct(
    string $value
  ) {
    $this->validateValue($value);

    if($this->hasViolations()) {
      $this->value = false;
      return;
    }

    $this->hashValue($value);
  }

  private function validateValue(string $value): void
  {
    if (strlen($value) < 12) {
      $this->violations[] = 'Password must have at least 12 characteres';
    }

    if (!preg_match('/[!@\$%#&]/', $value)) {
      $this->violations[] = 'Password must have at least one of following special characters !@$%#&';
    }

    if (!preg_match('/[A-Z]/', $value)) {
      $this->violations[] = 'Password must have at least one letter in uppercase';
    }

    if (!preg_match('/[a-z]/', $value)) {
      $this->violations[] = 'Password must have at least one letter in lowercase';
    }
  }

  public function hasViolations(): bool
  {
    return !empty($this->violations);
  }

  private function hashValue(string $value): void
  {
    $this->value = password_hash($value, $this->algo);

    if($this->value === false) {
      throw new \DomainException('Error while hashing the password');
    }

    if($this->value === null) {
      throw new \DomainException('Invalid algorithm to hash password');
    }
  }

  /**
   * Password hash value
   */
  public function getValue(): string|false
  {
    return $this->value;
  }

  /**
   * @return string[]
   */
  public function getViolations(): array
  {
    return $this->violations;
  }
}
