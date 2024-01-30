<?php declare(strict_types=1);

namespace Api\Auth\Infra;

use Api\RequestValidator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

final class RevokeAuthController
{
  private array $requiredFields = [];
  private array $requiredCookies = ['p_session_id'];

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

    $response = new JsonResponse(['request' => $this->validator->request()->all(), 'received_cookies' => $this->validator->request()->cookies->all()]);

    $pSessionId = new Cookie(
      name:     'p_session_id',
      value:    'default',
      expire:   time() + (3600 * 24 * 10), //10 dias
      path:     '/',
      domain:   '.pieam.dev',
      secure:   true,
      httpOnly: true,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    return $response->withoutCookie($pSessionId);
  }
}
