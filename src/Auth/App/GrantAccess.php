<?php declare(strict_types=1);

namespace Api\Auth\App;

use Api\Auth\App\Service\DataChangeCheckerService;
use Api\Auth\Domain\Entity\User;
use Api\Auth\Infra\Repository\UserRepository;

final class GrantAccess
{
  public function __construct(
    private UserRepository $repository,
    private DataChangeCheckerService $dataChangeCheckerService
  ){
  }

  public function handle(GrantAccessInput $input): GrantAccessOutput
  {
    $user = $this->repository->getUserByGoogleId($input->googleId);

    if($this->repository->hasErrors()) {
      return new GrantAccessOutput(false, $user, $this->repository->getErrors());
    }

    if(!$user) {
      $user = new User($input->firstName, $input->lastName, $input->email, $input->googleId, $input->imageUrl);
      $id = $this->repository->createUser($user);

      if(!$id and $this->repository->hasErrors()) {
        return new GrantAccessOutput(false, $user, $this->repository->getErrors());
      }

      $user->setId($id);

      return new GrantAccessOutput(true, $user, $this->repository->getErrors());
    }

    $updatedUser = $this->dataChangeCheckerService->verifyUserDataUpdate($input, $user);

    if(!$updatedUser) {
      return new GrantAccessOutput(true, $user, $this->repository->getErrors());
    }

    if(!$this->repository->updateUser($updatedUser)) {
      $defaultError['text'] = 'An error occours when try to update user with most recent data from google account. We will try again in next login';
      $defaultError['kind'] = 'warning';

      $errors = $this->repository->hasErrors() ? $this->repository->getErrors() : $defaultError;

      return new GrantAccessOutput(true, $user, $errors);
    }

    return new GrantAccessOutput(true, $updatedUser, $this->repository->getErrors());
  }
}
