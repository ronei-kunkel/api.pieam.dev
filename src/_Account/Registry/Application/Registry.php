<?php declare(strict_types=1);

namespace Api\Account\Registry\Application;
use Api\Account\Registry\Domain\Email;
use Api\Account\Registry\Domain\Name;
use Api\Account\Registry\Domain\Password;
use Api\Account\Registry\Domain\User;
use Api\Account\Registry\Infrastructure\Repository;

final class Registry
{
  public function __construct(
    private Repository $repository
  ) {
  }

  public function handle(RegistryInput $input): RegistryOutput
  {
    $user = new User(new Name($input->name), new Email($input->email), new Password($input->password));

    if($user->hasViolations()) {
      return new RegistryOutput(false, $user->getViolations(), 400);
    }

    $this->repository->create($user);

    if($this->repository->hasViolations()) {
      return new RegistryOutput(false, $this->repository->getViolations(), 500);
    }

    return new RegistryOutput(true, [], 201);
  }
}
