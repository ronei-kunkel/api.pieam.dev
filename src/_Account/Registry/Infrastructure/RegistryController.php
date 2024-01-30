<?php declare(strict_types=1);

namespace Api\Account\Registry\Infrastructure;

use Api\Account\Registry\Application\Registry;
use Api\Account\Registry\Application\RegistryInput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RegistryController
{
  private array $requiredFields = ['name', 'email', 'password'];

  public function __construct(
    private Request $request,
    private RequiredFieldsValidator $validator,
    private Registry $registry
  ) {
  }

  public function __invoke(): JsonResponse
  {
    if($this->validator->validateRequiredFields($this->requiredFields)) {
      return new JsonResponse(['registered' => false, 'errors' => ['missing_fields' => $this->validator->getMissingFields()]], 422);
    }

    $registryInput = new RegistryInput($this->request->name ?? '', $this->request->email ?? '', $this->request->password ?? '');

    $registryOutput = $this->registry->handle($registryInput);

    return new JsonResponse(['registered' => $registryOutput->status, 'errors' => $registryOutput->errors], $registryOutput->code);
  }
}
