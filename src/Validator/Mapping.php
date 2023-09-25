<?php

namespace App\Validator;

class MappingValidator
{
  private $requiredFields;
  private $optionalFields;

  public function __construct()
  {
    $this->requiredFields = [
      'object_oid',
      'object_sectorName',
      'object_latitude',
      'object_longitude',
      'field_value'
    ];
    $this->optionalFields = [
      'object_categoryName',
      'object_code'
    ];
  }

  public function getRequiredFields()
  {
    return $this->requiredFields;
  }

  public function getOptionalFields()
  {
    return $this->optionalFields;
  }

  public function validate(array $headers, array $mapping): array
  {
    $errors = [];
    $missingKeys = array_diff($headers, array_keys($mapping));
    if (count($missingKeys) > 0) {
      $errors[] = "Keys not mapped: " . implode(', ', $missingKeys);
    }

    $required = $this->getRequiredFields();
    $missingRequired = array_diff($required, array_values($mapping));
    if (count($missingRequired) > 0) {
      $errors[] = "Required fields not found: " . implode(', ', $missingRequired);
    }

    $is_valid = count($errors) > 0 ? false : true;

    return [ 'validation' => $is_valid, 'errors' => $errors];
  }
}
