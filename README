XSD schema validation constraint for PHPUnit
---

## Installation
```
   composer install jasny/phpunit-xsdvalidation
```

## Usage
```
   $constraint = new Jasny\PHPUnit\Constraint\XSDValidation("myschema.xsd");

   $xml = $this->object->doSomething();
   $this->assertThat($xml, $constraint);
```

## Usage with mock object
```
   $constraint = new Jasny\PHPUnit\Constraint\XSDValidation("myschema.xsd");

   $mock = $this->getMock('Foo\Bar', ['doSomething']);
   $mock->expects($this->once())
        ->method('doSomething')
        ->with($constraint);
```
