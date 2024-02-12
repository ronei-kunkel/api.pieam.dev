<?php declare(strict_types=1);

namespace Api\Auth\Infra\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

final class RevokeAuthController
{
  public function __construct(
    private Request $request,
  ) {
  }

  public function __invoke(): JsonResponse
  {
    session()->getHandler()->destroy($this->request->cookies->get(env('SESSION_COOKIE')));

    $pSessionVerificationCookie = new Cookie(
      name: 'p_session_verification',
      value: '0',
      expire: 0,
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: false,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    return (new JsonResponse())->withCookie($pSessionVerificationCookie)
      ->withoutCookie(cookie: env('SESSION_COOKIE'), path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: 'p_csrf_token', path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: 'p_csrf_cookie', path: '/', domain: '.pieam.dev');
  }
}
