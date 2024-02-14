<?php declare(strict_types=1);

namespace Api\Auth\Infra\Controller;

use Api\_Common\Infra\Repository\SessionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

final class RevokeAuthController
{
  public function __construct(
    private Request $request,
    private SessionRepository $sessionRepository,
  ) {
  }

  public function __invoke(): JsonResponse
  {
    if (!$this->sessionRepository->destroy($this->request->cookies->get(env('SESSION_COOKIE')))) {
      return (new JsonResponse(status: 500));
    }

    return (new JsonResponse())->withoutCookie(cookie: 'p_session_verification', path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: env('SESSION_COOKIE'), path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: 'p_csrf_token', path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: 'p_csrf_cookie', path: '/', domain: '.pieam.dev');
  }
}
