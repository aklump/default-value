<?php

namespace AKlump\DefaultValue;

use Exception;
use ReflectionClass;
use ReflectionException;
use stdClass;

/**
 * Computes default values by type.
 */
class DefaultValue {

  /**
   * Get a default value by type.
   *
   * @param string $type
   *   The variable type or object classname.
   *
   * @return array|false|float|int|mixed|\stdClass|string|null
   * @throws \AKlump\DefaultValue\IndeterminateDefaultValueException
   *   When the default value cannot be determined.
   */
  public static function get(string $type) {
    if (strstr($type, '\\')) {
      return static::getDefaultFromClassname($type);
    }

    switch (strtolower($type)) {
      case 'null':
        return NULL;

      case 'object':
        return new stdClass();

      case 'array':
        return [];

      case 'bool':
      case 'boolean':
        return FALSE;

      case 'float':
      case 'double':
        return floatval(NULL);

      case 'number':
      case 'int':
      case 'integer':
        return 0;

      case 'string':
        return '';
    }

    throw new IndeterminateDefaultValueException($type, 'that variable type is not understood.');
  }

  /**
   * Try to get a default value from a fully-qualified name.
   *
   * @param string $classname
   *   The fully-qualified classname.
   *
   * @return mixed
   *   The default value if it can be determined.
   * @throws \AKlump\DefaultValue\IndeterminateDefaultValueException
   */
  protected static function getDefaultFromClassname(string $classname) {
    try {
      $class = new ReflectionClass($classname);
    }
    catch (ReflectionException $exception) {
      throw new IndeterminateDefaultValueException($classname, $exception->getMessage(), IndeterminateDefaultValueException::OBJ_MISSING_CLASS, $exception);
    }
    if (!$class->isInstantiable()) {
      throw new IndeterminateDefaultValueException($classname, sprintf('"%s" is not instantiable.', $classname), IndeterminateDefaultValueException::OBJ_CANNOT_INSTANTIATE);
    }

    try {
      $constructor = $class->getMethod('__construct');
      $constructor_param_count = $constructor->getNumberOfRequiredParameters();
      $can_construct = 0 === $constructor_param_count;
    }
    catch (\Exception $exception) {
      $can_construct = TRUE;
    }
    if ($can_construct) {
      return new $classname();
    }

    // Constructor requires one or more parameters.
    throw new IndeterminateDefaultValueException($classname, sprintf('"%s" has a __construct() method but it requires %d parameters.', $classname, $constructor_param_count), IndeterminateDefaultValueException::OBJ_REQUIRES_PARAM);
  }

}
