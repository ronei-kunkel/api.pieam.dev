<?php declare(strict_types=1);

namespace Api\_Common\Infra\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

final class CsrfValidation
{
  /**
   * Validate if the csrf token sent are valid
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\JsonResponse)  $next
   */
  public function handle(Request $request, \Closure $next): JsonResponse
  {
    $rawValue = $request->cookies->get('p_csrf_cookie');

    $hashedValue = $request->input('p_csrf_token');

    if (!$rawValue or !$hashedValue or sha1($rawValue) !== $hashedValue ) {

      $response = new JsonResponse(status: 400);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    $res = $next($request);
    return $res;
  }
}
