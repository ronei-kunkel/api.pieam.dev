<?php declare(strict_types=1);

namespace Api\Account\RevokeAccess\Infrastructure;

use Api\RequestValidator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

final class RevokeAccessController
{
  private array $requiredFields = ['csrf_token'];
  private array $requiredCookies = ['x-csrf_token', '__Secure-SSID', 'h'];

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

    $hasSession = new Cookie(
      name: 'h',
      value: '0',
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: false
    );

    $response = new JsonResponse(['request' => $this->validator->request()->all(), 'received_cookies' => $this->validator->request()->cookies->all()]);

    return $response->withoutCookie(cookie:'__Secure-SSID', path:'/', domain:'.pieam.dev')->withCookie($hasSession);
  }
}
