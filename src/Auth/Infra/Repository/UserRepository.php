<?php declare(strict_types=1);

namespace Api\Auth\Infra\Repository;

use Api\Auth\Domain\Entity\User;
use Illuminate\Support\Facades\DB;

final class UserRepository
{
  private array $errors = [];

  public function getUserByGoogleId(string $googleId): ?User
  {
    try {
      $userData = DB::table('user')->get()->where('google_id', '=', $googleId)->first();

      if (!$userData) {
        return null;
      }

      $user = new User($userData->name, $userData->last_name, $userData->email, $userData->google_id, $userData->image_url);

      return $user->setId($userData->id);

    } catch (\Throwable $e) {

      $this->errors['text'] = 'Cannot find user because internal error';
      $this->errors['kind'] = 'error';

      $this->errors['private']['text'] = $e->getMessage();
      $this->errors['private']['code'] = $e->getCode();

      return null;
    }
  }

  public function createUser(User $user): ?int
  {
    try {

      $id = DB::table('user')->insertGetId([
        'name'      => $user->getName(),
        'last_name' => $user->getLastName(),
        'email'     => $user->getEmail(),
        'google_id' => $user->getGoogleId(),
        'image_url' => $user->getImageUrl(),
      ]);

      if (!$id) {
        $this->errors['text'] = 'User cannot be registered because internal unknown error. Try again later';
        $this->errors['kind'] = 'error';

        return null;
      }

      return $id;

    } catch (\Throwable $e) {

      $this->errors['text'] = 'User cannot be registered because internal error';
      $this->errors['kind'] = 'error';

      $this->errors['private']['text'] = $e->getMessage();
      $this->errors['private']['code'] = $e->getCode();

      return null;
    }
  }

  public function updateUser(User $user): bool
  {
    try {

      DB::table('user')->where('google_id', '=', $user->getGoogleId())->update([
        'name'      => $user->getName(),
        'last_name' => $user->getLastName(),
        'email'     => $user->getEmail(),
        'image_url' => $user->getImageUrl(),
      ]);

      return true;

    } catch (\Throwable $e) {

      $this->errors['text'] = 'User data cannot updated with most recent data provides by google because internal error. We will try again in next login';
      $this->errors['kind'] = 'warning';

      $this->errors['private']['text'] = $e->getMessage();
      $this->errors['private']['code'] = $e->getCode();

      return false;
    }
  }

  public function hasErrors(): bool
  {
    return !empty($this->errors);
  }

  public function getErrors(): array
  {
    // log of violations

    if (env('APP_DEBUG')) {
      return $this->errors;
    }

    unset($this->errors['private']);
    return $this->errors;
  }
}