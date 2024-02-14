<?php declare(strict_types=1);

namespace Api\User\Infra\Controller;

use Api\_Common\Infra\Repository\SessionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class InfoController
{

  public function __construct(
    private Request $request,
    private SessionRepository $sessionRepository
  ) {
  }

  public function __invoke(): JsonResponse
  {
    $sessionId = $this->request->cookies->get(env('SESSION_COOKIE'));

    $session = $this->sessionRepository->get($sessionId);

    $content['id']        = $session->user()->getId();
    $content['name']      = $session->user()->getName();
    $content['last_name'] = $session->user()->getLastName();
    $content['email']     = $session->user()->getEmail();
    $content['image_url'] = $session->user()->getImageUrl();

    return new JsonResponse($content, 200);
  }
}
