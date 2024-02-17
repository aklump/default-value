<?php

namespace AKlump\DefaultValue\Tests\Unit;

use AKlump\DefaultValue\DefaultValue;
use AKlump\DefaultValue\IndeterminateDefaultValueException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\DefaultValue\DefaultValue
 * @uses   \AKlump\DefaultValue\IndeterminateDefaultValueException
 */
class DefaultValueTest extends TestCase {

  public function testUnknownTypeThrows() {
    $this->expectException(IndeterminateDefaultValueException::class);
    $this->expectExceptionMessageMatches('#that variable type is not understood#');
    DefaultValue::get('zebra');
  }

  public function testRequiredConstructorClassThrows() {
    $this->expectException(IndeterminateDefaultValueException::class);
    $this->expectExceptionMessageMatches('#has a __construct\(\) method but it requires#');
    DefaultValue::get(HasRequiredConstructorArgs::class);
  }

  public function testAbstractClassThrows() {
    $this->expectException(IndeterminateDefaultValueException::class);
    $this->expectExceptionMessageMatches('#is not instantiable#');
    DefaultValue::get(NotInstantiableAbstract::class);
  }

  public function testMissingClassThrows() {
    $this->expectException(IndeterminateDefaultValueException::class);
    $this->expectExceptionMessageMatches('#does not exist#');
    DefaultValue::get('\MyBogus\Class');
  }

  public function dataFortestInvokeProvider() {
    $tests = [];
    $tests[] = [
      new FooBarWithConstructorMethod(),
      FooBarWithConstructorMethod::class,
    ];
    $tests[] = [new FooBar(), FooBar::class];
    $tests[] = [[], 'array'];
    $tests[] = [new \stdClass(), 'object'];
    $tests[] = [NULL, 'null'];
    $tests[] = [FALSE, 'bool'];
    $tests[] = [FALSE, 'boolean'];
    $tests[] = [0, 'number'];
    $tests[] = [0, 'integer'];
    $tests[] = [0, 'int'];
    $tests[] = ['', 'string'];
    $tests[] = [0.0, 'float'];
    $tests[] = [0.0, 'double'];

    return $tests;
  }

  /**
   * @dataProvider dataFortestInvokeProvider
   */
  public function testInvoke($expected, string $type) {
    $default = DefaultValue::get($type);
    if (is_object($expected)) {
      $this->assertEquals($expected, $default);
    }
    else {
      $this->assertSame($expected, $default);
    }
  }
}

class FooBar {

}

class FooBarWithConstructorMethod {

  public function __construct(string $foo = NULL) {
  }

}

abstract class NotInstantiableAbstract {

}

class HasRequiredConstructorArgs {

  public function __construct(string $json) {

  }
}
