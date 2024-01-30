<?php declare(strict_types=1);

namespace Api\Account\Registry\Domain;

final class User
{
  private array $violations = [];

  public function __construct(
    private Name $name,
    private Email $email,
    private Password $password
  ) {
    $this->validateValues();
  }

  private function validateValues(): void
  {
    if($this->name->hasViolations()) {
      $this->violations['name'] = $this->name->getViolations();
    }

    if($this->email->hasViolations()) {
      $this->violations['email'] = $this->email->getViolations();
    }

    if($this->password->hasViolations()) {
      $this->violations['password'] = $this->password->getViolations();
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

  public function getName(): Name
  {
    return $this->name;
  }

  public function getEmail(): Email
  {
    return $this->email;
  }

  public function getPassword(): Password
  {
    return $this->password;
  }
}
