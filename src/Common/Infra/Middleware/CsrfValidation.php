<?php declare(strict_types=1);

namespace Api\Common\Infra\Middleware;

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

    $sessionRawValue = $request->session()->get('_token');

    if (!$rawValue) {
      /**
       * @todo[FLOW] implementar response com redirect para a home do pieam.dev
       * com algum cookie ou flash message para transportar a mensagem
       * de que houve falha de comunicação do google com o sistema durante o fluxo de login
       * 
       * @todo[FRONT] implementar alguma forma de ler essa mensagem desse cookie ou outro lugar caso ela exista
       */
      $content['text'] = 'Missing CSRF cookie';
      $content['kind'] = 'error';

      $response = new JsonResponse($content, 400);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    if (!$hashedValue) {
      /**
       * @todo[FLOW] implementar response com redirect para a home do pieam.dev
       * com algum cookie ou flash message para transportar a mensagem
       * de que houve falha de comunicação do google com o sistema durante o fluxo de login
       * 
       * @todo[FRONT] implementar alguma forma de ler essa mensagem desse cookie ou outro lugar caso ela exista
       */
      $content['text'] = 'Missing CSRF token';
      $content['kind'] = 'error';

      $response = new JsonResponse($content, 400);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    if (sha1($rawValue) !== $hashedValue or $rawValue !== $sessionRawValue) {
      /**
       * @todo[FRONT] implementar alguma forma de ler essa mensagem desse cookie ou outro lugar caso ela exista
       */
      $content['text'] = 'Wrong CSRF token';
      $content['kind'] = 'error';

      $response = new JsonResponse($content, 400);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    $res = $next($request);
    return $res;
  }
}
