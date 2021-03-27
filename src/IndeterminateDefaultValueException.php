<?php

namespace AKlump\DefaultValue;

/**
 * Thrown when a default value cannot be determined.
 */
class IndeterminateDefaultValueException extends \Exception {

  /**
   * @var int
   */
  const OBJ_MISSING_CLASS = 1;

  /**
   * @var int
   */
  const OBJ_CANNOT_INSTANTIATE = 2;

  /**
   * @var int
   */
  const OBJ_NO_CONSTRUCTOR = 3;

  /**
   * @var int
   */
  const OBJ_REQUIRES_PARAM = 4;

  /**
   * IndeterminateDefaultValueException constructor.
   *
   * @param string $type
   *   The variable type that was used to determine the default value.
   * @param string $reason
   *   The reason it cannot be determined.
   * @param int $code
   *   [optional] The Exception code.
   * @param \Throwable $previous
   *   [optional] The previous throwable used for the exception chaining.
   */
  public function __construct(string $type, string $reason, $code = 0, \Throwable $previous = NULL) {
    $message = sprintf('Cannot determine a default value from "%s"; %s', $type, rtrim(lcfirst($reason), '.') . '... ');
    parent::__construct($message, $code, $previous);
  }

}
