<?php declare(strict_types=1);

namespace Api\Auth\Infra\Service;

use Google_Client;
use Throwable;

final class GoogleService
{

  private array $userData = [];
  private string $clientId;

  /**
   * @todo ver se é possível validar o jwt com algum valor do json do cliente oauth (https://console.cloud.google.com/apis/credentials?hl=pt-BR&project=pieam-410602)
   */
  public function __construct()
  {
    $this->clientId = env('GOOGLE_CLIENT_ID');
  }

  public function validateJwt(string $jwt): bool
  {
    $client = new Google_Client(['client_id' => $this->clientId]);

    try {
      $userData = $client->verifyIdToken($jwt);
    } catch (Throwable $th) {
      return false;
    }

    if (!$userData) {
      return false;
    }

    $this->userData = $userData;

    return true;
  }

  public function getUserData(): array
  {
    return $this->userData;
  }
}