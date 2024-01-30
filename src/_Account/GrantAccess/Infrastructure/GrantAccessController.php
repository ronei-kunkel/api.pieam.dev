<?php declare(strict_types=1);

namespace Api\Account\GrantAccess\Infrastructure;

use Api\RequestValidator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

final class GrantAccessController
{
  private array $requiredFields = ['jwt', 'csrf_token'];
  private array $requiredCookies = ['x-csrf_token'];

  public function __construct(
    private RequestValidator $validator
  ) {
  }

  public function __invoke(): JsonResponse
  {
    if($this->validator->checkCookies($this->requiredCookies)) {
      return new JsonResponse(['access' => 'failed', 'errors' => ['invalid_token' => $this->validator->getMissingCookies()]], 422);
    }

    if($this->validator->checkBody($this->requiredFields)) {
      return new JsonResponse(['access' => 'failed', 'errors' => ['missing_fields' => $this->validator->getMissingFields()]], 422);
    }

    $ssidCookie = new Cookie(
      name: '__Secure-SSID',
      value: 'default',
      expire: time() + (3600 * 24 * 90), //90 dias
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: true
    );

    $hasSession = new Cookie(
      name: 'h',
      value: '1',
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: false
    );

    $response = new JsonResponse(['request' => $this->validator->request()->all(), 'received_cookies' => $this->validator->request()->cookies->all()]);

    return $response->withCookie($ssidCookie)->withCookie($hasSession);
  }
}
