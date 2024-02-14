<?php declare(strict_types=1);

namespace Api\_Common\Infra\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * When it needed, it on the first middleware position because it apply cookies in response AFTER return from Controller
 */
final class CsrfRenew
{
  /**
   * When it neede
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\JsonResponse)  $next
   */
  public function handle(Request $request, \Closure $next): JsonResponse
  {
    /**
     * @var JsonResponse
     * 
     * Response returned through controller
     */
    $response = $next($request);

    $time = time();

    $tenDaysTime = $time + (3600 * 24 * 10);

    $rawValue = (string) $time;

    $hashedValue = sha1($rawValue);

    $pCsrfCookie = new Cookie(
      name: 'p_csrf_cookie',
      httpOnly: true,
      value: $rawValue,
      expire: $tenDaysTime,
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    $pCsrfToken = new Cookie(
      name: 'p_csrf_token',
      httpOnly: false,
      value: $hashedValue,
      expire: $pCsrfCookie->getExpiresTime(),
      path: $pCsrfCookie->getPath(),
      domain: $pCsrfCookie->getDomain(),
      secure: $pCsrfCookie->isSecure(),
      sameSite: $pCsrfCookie->getSameSite(),
    );

    return $response->withCookie($pCsrfCookie)
      ->withCookie($pCsrfToken);
  }
}
