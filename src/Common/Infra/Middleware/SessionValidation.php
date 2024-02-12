<?php declare(strict_types=1);

namespace Api\Common\Infra\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

final class SessionValidation
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\JsonResponse)  $next
   */
  public function handle(Request $request, \Closure $next): JsonResponse
  {
    $sessionCookie = env('SESSION_COOKIE');

    if (session()->getId() !== $request->cookies->get($sessionCookie)) {

      $content['text'] = 'invalid session';
      $content['kind'] = 'error';

      $response = new JsonResponse($content, 401);

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

      $response->withCookie($pSessionVerificationCookie)
        ->withoutCookie(cookie: $sessionCookie, path: '/', domain: '.pieam.dev')
        ->withoutCookie(cookie: 'p_csrf_token', path: '/', domain: '.pieam.dev')
        ->withoutCookie(cookie: 'p_csrf_cookie', path: '/', domain: '.pieam.dev');

      return $response;
    }

    $res = $next($request);
    return $res;
  }
}
