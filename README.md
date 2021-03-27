# Default Value

A small utility to return a default value or instance based on a variable type or
classname.

## Vanilla PHP

```php
<?php

$variable_types = [
  'array',
  'double',
  'float',
  'integer',
  'null',
  'object',
  'string',

  // In addition you can pass fully-qualified class names, so long as their
  // constructors do not REQUIRE any parameters.
  '\Foo\Bar\Baz',
];

foreach ($variable_types as $variable_type) {
  $default_value = \AKlump\DefaultValue\DefaultValue::get($variable_type);
}
```

## Drupal 8+ Integration

When using within a Drupal installation use the
class `\Drupal\Component\Utility\DefaultValue` and you'll get special Drupal
support, in addition to the vanilla PHP explained above.

```php
<?php

$special_drupal_variable_types = [

  // This is a service ID.
  '@current_user',
  
  // This class has a ::create method with no required arguments.
  '\Drupal\user\Entity\User',

  // This class implements ContainerInjectionInterface.
  '\Drupal\system\Controller\CsrfTokenController',
];

foreach ($special_drupal_variable_types as $variable_type) {
  $default_value = \Drupal\Component\Utility\DefaultValue::get($variable_type);
}
```
