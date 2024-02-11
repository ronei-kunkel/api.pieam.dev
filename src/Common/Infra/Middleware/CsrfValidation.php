<?php declare(strict_types=1);

namespace Api\Common\Infra\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

final class CsrfValidation
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\JsonResponse)  $next
   */
  public function handle(Request $request, \Closure $next): JsonResponse
  {
    if(!$request->cookies->has('p_csrf_cookie')) {
      /**
       * @todo[FLOW] implementar response com redirect para a home do pieam.dev
       * com algum cookie ou flash message para transportar a mensagem
       * de que houve falha de comunicação do google com o sistema durante o fluxo de login
       * 
       * @todo[FRONT] implementar alguma forma de ler essa mensagem desse cookie ou outro lugar caso ela exista
       */
      $content['message']['text'] = 'Missing CSRF cookie';
      $content['message']['kind'] = 'error';

      $response = new JsonResponse($content, 400);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    if(!$request->input('p_csrf_token')) {
      /**
       * @todo[FLOW] implementar response com redirect para a home do pieam.dev
       * com algum cookie ou flash message para transportar a mensagem
       * de que houve falha de comunicação do google com o sistema durante o fluxo de login
       * 
       * @todo[FRONT] implementar alguma forma de ler essa mensagem desse cookie ou outro lugar caso ela exista
       */
      $content['message']['text'] = 'Missing CSRF token';
      $content['message']['kind'] = 'error';

      $response = new JsonResponse($content, 400);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    $cookie = $request->cookies->get('p_csrf_cookie');

    $token = $request->input('p_csrf_token');

    if(password_verify($cookie, $token)) {
      /**
       * @todo[FLOW] implementar response com redirect para a home do pieam.dev
       * com algum cookie ou flash message para transportar a mensagem
       * de que houve falha de comunicação do google com o sistema durante o fluxo de login
       * 
       * @todo[FRONT] implementar alguma forma de ler essa mensagem desse cookie ou outro lugar caso ela exista
       */
      $content['message']['text'] = 'Wrong CSRF token';
      $content['message']['kind'] = 'error';

      $response = new JsonResponse($content, 400);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    Log::error('csrf', ['token'=>$token, 'cookie'=>$cookie]);

    return $next($request);
  }
}
