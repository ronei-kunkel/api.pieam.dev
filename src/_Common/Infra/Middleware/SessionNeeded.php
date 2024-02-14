<?php declare(strict_types=1);

namespace Api\_Common\Infra\Middleware;

use Api\_Common\Infra\Repository\SessionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

final class SessionNeeded
{

  /**
   * Session Needed
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\JsonResponse)  $next
   */
  public function handle(Request $request, \Closure $next): JsonResponse
  {
    $sessionCookie = env('SESSION_COOKIE');

    $sessionId = $request->cookies->get($sessionCookie);

    if (!$sessionId) {
      return $this->invalidSessionResponse($sessionCookie);
    }

    if (!(new SessionRepository())->has($sessionId)) {
      return $this->invalidSessionResponse($sessionCookie);
    }

    $response = $next($request);

    return $response;
  }

  /**
   * Build JsonResponse for invalid session
   */
  private function invalidSessionResponse($sessionCookie): JsonResponse
  {

    $response = new JsonResponse(status: 401);

    return $response->withoutCookie(cookie: 'p_session_verification', path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: $sessionCookie, path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: 'p_csrf_token', path: '/', domain: '.pieam.dev')
      ->withoutCookie(cookie: 'p_csrf_cookie', path: '/', domain: '.pieam.dev');
  }
}
