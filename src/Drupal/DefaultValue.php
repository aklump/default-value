<?php

namespace Drupal\Component\Utility;

use AKlump\DefaultValue\DefaultValue as DefaultValueBase;
use AKlump\DefaultValue\IndeterminateDefaultValueException;
use Drupal;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Adds additional means of instantiating unique to Drupal 8+.
 *
 * - Return instances of services, e.g. '@current_user'.  Due to performance you
 * will not get a service instance when you pass the classname--you must pass
 * the service id instead.
 * - Return instances of classes implementing ContainerInjectionInterface
 * - Return instance of classes having ::create() methods with no parameters.
 */
final class DefaultValue extends DefaultValueBase {

  /**
   * {@inheritdoc}
   */
  public static function get(string $type) {
    if (substr($type, 0, 1) === '@') {
      try {
        return Drupal::service(trim($type, '@'));
      }
      catch (ServiceNotFoundException $exception) {
        throw new IndeterminateDefaultValueException($type, $exception->getMessage(), IndeterminateDefaultValueException::OBJ_MISSING_CLASS, $exception);
      }
    }

    return parent::get($type);
  }

  /**
   * {@inheritdoc}
   */
  protected static function getDefaultFromClassname(string $classname) {
    try {
      $class = new ReflectionClass($classname);
      if ($class->implementsInterface(ContainerInjectionInterface::class)) {
        return $classname::create(Drupal::getContainer());
      }
    }
    catch (ReflectionException $exception) {
      throw new IndeterminateDefaultValueException($classname, $exception->getMessage(), IndeterminateDefaultValueException::OBJ_MISSING_CLASS, $exception);
    }

    foreach ($class->getMethods(ReflectionMethod::IS_STATIC) as $method) {
      if ($method->getShortName() === 'create' && $method->getNumberOfRequiredParameters() === 0) {
        return $classname::create();
      }
    }

    return parent::getDefaultFromClassname($classname);
  }

}
