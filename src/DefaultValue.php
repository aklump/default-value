<?php

namespace AKlump\DefaultValue;

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
        return new \stdClass();

      case 'array':
        return [];

      case 'boolean':
        return FALSE;

      case 'float':
      case 'double':
        return floatval(NULL);

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
      $class = new \ReflectionClass($classname);
    }
    catch (\ReflectionException $exception) {
      throw new IndeterminateDefaultValueException($classname, $exception->getMessage(), IndeterminateDefaultValueException::OBJ_MISSING_CLASS, $exception);
    }
    if (!$class->isInstantiable()) {
      throw new IndeterminateDefaultValueException($classname, sprintf('"%s" is not instantiable.', $classname), IndeterminateDefaultValueException::OBJ_CANNOT_INSTANTIATE);
    }

    // See if we have __construct() with no required parameters.
    try {
      $constructor = $class->getMethod('__construct');
      $constructor_param_count = $constructor->getNumberOfRequiredParameters();
      if ($constructor_param_count === 0) {
        return new $classname();
      }
    }
    catch (\Exception $exception) {
      throw new IndeterminateDefaultValueException($classname, sprintf('"%s" has no __constructor() method.', $classname, IndeterminateDefaultValueException::OBJ_NO_CONSTRUCTOR));
    }

    // Constructor requires one or more parameters.
    throw new IndeterminateDefaultValueException($classname, sprintf('"%s" has a __construct() method but it requires %d parameters.', $classname, $constructor_param_count), IndeterminateDefaultValueException::OBJ_REQUIRES_PARAM);
  }

}
