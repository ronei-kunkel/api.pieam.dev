<?php declare(strict_types=1);

namespace Api\Auth\Infra\Controller;

use Api\_Common\Domain\Entity\Session;
use Api\_Common\Infra\Repository\SessionRepository;
use Api\Auth\App\UseCase\GrantAccess;
use Api\Auth\App\UseCase\GrantAccessInput;
use Api\Auth\Infra\Service\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

final class GoogleAuthController
{
  public function __construct(
    private Request $request,
    private GoogleService $googleService,
    private GrantAccess $grantAccess,
    private SessionRepository $sessionRepository
  ) {
  }

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

    $session = new Session($output->user);

    $sessionId = $this->sessionRepository->save($session);

    if(!$sessionId) {
      return new JsonResponse(status: 500);
    }

    $sessionCookie = env('SESSION_COOKIE');

    $pSessionIdCookie = new Cookie(
      name: $sessionCookie,
      value: $sessionId,
      expire: time() + (3600 * 24 * 10),
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: true,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    $pSessionVerificationCookie = new Cookie(
      name: 'p_session_verification',
      value: '1',
      expire: time() + (3600 * 24 * 10),
      path: '/',
      domain: '.pieam.dev',
      secure: true,
      httpOnly: false,
      sameSite: Cookie::SAMESITE_STRICT,
    );

    return (new JsonResponse())->withCookie($pSessionIdCookie)
    ->withCookie($pSessionVerificationCookie);
  }
}
