<?php declare(strict_types=1);

namespace Api\Auth\Infra\Controller;

use Api\Auth\App\GrantAccess;
use Api\Auth\App\GrantAccessInput;
use Api\Auth\Infra\Service\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
   * @todo[CRYPT] Retornar o p_csrf_cookie(httpOnly) com valor não criptografado e o p_csrf_token(não httpOnly) com o valor criptografado (usando password_hash [entender questão de limite de caracteres dos cookies]). Quando a api receber uma requisição post, faz um password_verify do valor do p_csrf_cookie com a hash que é o valor do p_csrf_token
   */
  public function __invoke(): JsonResponse
  {
    $input = new GrantAccessInput(
      firstName: $this->request->input('given_name'),
      lastName: $this->request->input('family_name'),
      email: $this->request->input('email'),
      googleId: $this->request->input('sub'),
      imageUrl: $this->request->input('picture')
    );

    /**
     * @todo[OUTPUT] implementar onde acontecem os erros, qual http code deve ser retornado
     */
    $output = $this->grantAccess->handle($input);

    if (!$output->success) {
      return new JsonResponse($output->errors, 422);
    }

    $tenDaysTime = time() + (3600 * 24 * 10); //10 dias em segundos

    $user = new \stdClass();

    $user->id       = $output->user->getId();
    $user->googleId = $output->user->getGoogleId();
    $user->email    = $output->user->getEmail();
    $user->name     = $output->user->getName();
    $user->lastName = $output->user->getLastName();
    $user->imageUrl = $output->user->getImageUrl();

    $this->request->session()->put('user', $user);

    // @todo[CSRF] must be other value
    $csrfCookieValue = (string) time();

    $csrfTokenValue = password_hash($csrfCookieValue, PASSWORD_BCRYPT, ['cost' => 4]);

    /**
     * Auto send to front in next post requests
     * Need to bee revalidated in each post request received
     */
    $pCsrfCookie = new Cookie(
      name: 'p_csrf_cookie',
      httpOnly: true,
      value: $csrfCookieValue,
      expire: $tenDaysTime,
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    /**
     * Used to put on hidden input into forms
     * Need to bee revalidated in each post request received
     */
    $pCsrfToken = new Cookie(
      name: 'p_csrf_token',
      httpOnly: false,
      value: $csrfTokenValue,
      expire: $pCsrfCookie->getExpiresTime(),
      path: $pCsrfCookie->getPath(),
      domain: $pCsrfCookie->getDomain(),
      secure: $pCsrfCookie->isSecure(),
      sameSite: $pCsrfCookie->getSameSite(),
    );

    /**
     * Used to set the id of user session.
     * User info and session values must be setted here
     * 
     * It is http only, then is auto send into all request by browser
     * 
     * @todo[CRYPT] o valor do cookie de sessão deve estart criptografado ao enviar e ao receber nas requests deve ser descriptografado
     */
    $pSessionIdCookie = new Cookie(
      name: env('SESSION_COOKIE'),
      value: $this->request->session()->getId(),
      expire: $tenDaysTime,
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: true,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    /**
     * Used to validate if have session.
     * 
     * As the sesseion id is http only, this cookie are used to verify in front if session are defined
     */
    $pSessionVerificationCookie = new Cookie(
      name: 'p_session_verification',
      value: '1',
      expire: 0,
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: false,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    $content['message']['kind'] = 'success';

    $response = new JsonResponse($content, 200);

    return $response->withCookie($pSessionIdCookie)
      ->withCookie($pSessionVerificationCookie)
      ->withCookie($pCsrfCookie)
      ->withCookie($pCsrfToken);
  }
}
