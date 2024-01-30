<?php declare(strict_types=1);

namespace Api\Auth\Infra\Middleware;

use Api\Auth\Infra\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class GoogleJwtValidator
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, \Closure $next): Response
  {
    $jwt = $request->input('credential');

    $googleService = new GoogleService;

    if(!$googleService->validateJwt($jwt)) {
      /**
       * @todo[FLOW] implementar response com redirect para a home do pieam.dev
       * com algum cookie ou flash message para transportar a mensagem
       * de que houve falha de comunicação do google com o sistema durante o fluxo de login
       * 
       * @todo[FRONT] implementar alguma forma de ler essa mensagem desse cookie ou outro lugar caso ela exista
       */
      $content['message']['text'] = 'Invalid jwt';
      $content['message']['kind'] = 'error';

      $response = new Response(content: $content, status: 400, headers: ['Content-Type' => 'application/json']);

      // @todo[LOG] aplicar log do que foi recebido e do que será enviado

      return $response;
    }

    $userData = $googleService->getUserData();

    $request->merge($userData);

    return $next($request);
  }
}
