<?php declare(strict_types=1);

namespace Api\Auth\Infra;

use Api\Auth\App\GrantAccess;
use Api\Auth\App\GrantAccessInput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

final class GoogleAuthController
{
  public function __construct(
    private Request $request,
    private GoogleService $googleService,
    private GrantAccess $grantAccess
  ) {
  }

  /**
   * @todo[SESSION] Criar sessão para o usuário e retornar o id no cookie
   * @todo[CRYPT] Retornar o p_csrf_cookie(httpOnly) com valor não criptografado e o p_csrf_token(não httpOnly) com o valor criptografado (usando password_hash [entender questão de limite de caracteres dos cookies]). Quando a api receber uma requisição post, faz um password_verify do valor do p_csrf_cookie com a hash que é o valor do p_csrf_token
   */
  public function __invoke(): JsonResponse
  {
    $input = new GrantAccessInput(
      firstName:  $this->request->input('given_name'),
      lastName:   $this->request->input('family_name'),
      email:      $this->request->input('email'),
      googleId:   $this->request->input('sub'),
      imageUrl:   $this->request->input('picture')
    );

    /**
     * @todo[OUTPUT] implementar onde acontecem os erros, qual http code deve ser retornado
     */
    $output = $this->grantAccess->handle($input);

    if(!$output->success) {
      return new JsonResponse($output->errors, 422);
    }

    echo "<pre style='margin-left:260px;'>";
    print_r($output);
    echo "</pre>";
    exit;

    $tenDaysTime = time() + (3600 * 24 * 10); //10 dias

    // @todo[CSRF] must be other value
    $csrfCookieValue = (string) time();

    $csrfTokenValue = password_hash($csrfCookieValue, PASSWORD_BCRYPT, ['cost' => 4]);

    $pCsrfCookie = new Cookie(
      name:     'p_csrf_cookie',
      httpOnly: true,
      value:    $csrfCookieValue,
      expire:   $tenDaysTime,
      path:     '/',
      domain:   '.pieam.dev',
      secure:   true,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    $pCsrfToken = new Cookie(
      name:     'p_csrf_token',
      httpOnly: false,
      value:    $csrfTokenValue,
      expire:   $pCsrfCookie->getExpiresTime(),
      path:     $pCsrfCookie->getPath(),
      domain:   $pCsrfCookie->getDomain(),
      secure:   $pCsrfCookie->isSecure(),
      sameSite: $pCsrfCookie->getSameSite(),
    );

    // @todo[CRYPT] o valor do cookie de sessão deve estart criptografado ao enviar e ao receber nas requests deve ser descriptografado
    $pSessionId = new Cookie(
      name:     'p_session_id',
      value:    'default',
      expire:   $tenDaysTime,
      path:     '/',
      domain:   '.pieam.dev',
      secure:   true,
      httpOnly: true,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    $response = new JsonResponse(['request' => $this->request->all(), 'received_cookies' => $this->request->cookies->all()]);

    return $response->withoutCookie(cookie: 'g_csrf_token', path: '/', domain: '.pieam.dev')->withCookie($pSessionId)->withCookie($pCsrfCookie)->withCookie($pCsrfToken);
  }
}
