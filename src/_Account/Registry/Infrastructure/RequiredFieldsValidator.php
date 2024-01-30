<?php declare(strict_types=1);

namespace Api\Account\Registry\Infrastructure;
use Illuminate\Http\Request;

final class RequiredFieldsValidator
{
  private array $missingFields = [];

  public function __construct(
    private Request $request
  ) {
  }

  public function validateRequiredFields(array $requiredFields): bool
  {
    $incomingFields = $this->request->only($requiredFields);

    $this->missingFields = array_values(array_diff($requiredFields, array_keys($incomingFields)));

    return $this->hasMissingFields();
  }

  private function hasMissingFields(): bool
  {
    return !empty($this->missingFields);
  }

  public function getMissingFields(): array
  {
    return $this->missingFields;
  }
}
