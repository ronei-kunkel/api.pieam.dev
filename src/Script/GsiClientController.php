<?php declare(strict_types=1);

namespace Api\Script;
use Illuminate\Http\Response;

final class GsiClientController
{
  public function __invoke(): Response
  {
    return new Response(content: file_get_contents(__DIR__.'/../../script/gsi-client.js'), headers: ['Content-Type' => 'application/javascript']);
  }
}
