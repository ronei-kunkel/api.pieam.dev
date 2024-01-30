<?php declare(strict_types=1);

namespace Api\Account\Registry\Infrastructure;

use Api\Account\Registry\Domain\User;
use Illuminate\Support\Facades\DB;

final class Repository
{
  private array $violations = [];

  public function create(User $user): void
  {
    try {

      $insertion = DB::table('user')->insert([
        'name'      => $user->getName()->getValue(),
        'email'     => $user->getEmail()->getValue(),
        'password'  => $user->getPassword()->getValue()
      ]);

      if(!$insertion) {
        $this->violations['registry'] = 'User cannot be registered because internal unknown error. Try again later';
      }

    } catch (\Throwable $e) {

      $this->violations['registry'][]           = 'User cannot be registered because internal error';
      $this->violations['internal']['message']  = $e->getMessage();
      $this->violations['internal']['code']     = $e->getCode();

    }
  }

  public function hasViolations(): bool
  {
    return !empty($this->violations);
  }

  public function getViolations(): array
  {
    if(env('APP_DEBUG')) {
      return $this->violations;
    }

    unset($this->violations['internal']);
    return $this->violations;
  }
}