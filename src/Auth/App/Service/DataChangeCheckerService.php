<?php declare(strict_types=1);

namespace Api\Auth\App\Service;

use Api\Auth\App\UseCase\GrantAccessInput;
use Api\_Common\Domain\Entity\User;

final class DataChangeCheckerService
{
  public function verifyUserDataUpdate(GrantAccessInput $input, User $user): ?User
  {
    $newUserData = new User($input->firstName, $input->lastName, $input->email, $input->googleId, $input->imageUrl);
    $newUserData->setId($user->getId());

    return ($user == $newUserData) ? null : $newUserData;
  }
}