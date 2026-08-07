pop-code
========

[![Build Status](https://github.com/popphp/pop-code/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-code/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-code)](http://cc.popphp.org/pop-code/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Generate Code](#generate-code)
* [Enums](#enums)
* [Attributes](#attributes)
* [Parse Code](#parse-code)

Overview
--------
`pop-code` provides the ability to dynamically generate PHP code on the fly
as well as parse and modify existing PHP code.

`pop-code` is a component of the [Pop PHP Framework](https://www.popphp.org/).

[Top](#pop-code)

Install
-------

Install `pop-code` using Composer.

    composer require popphp/pop-code

Or, require it in your composer.json file

    "require": {
        "popphp/pop-view" : "^5.0.5"
    }

[Top](#pop-code)

Quickstart
----------

### Create a simple function

In this example, a function is created and rendered to a string:

```php
use Pop\Code\Generator;

$function = new Generator\FunctionGenerator('sayHello');
$function->addArgument('name', null, 'string');
$function->setBody("echo 'Hello ' . \$name;");
$function->addReturnType('void');
$function->setDesc('This is the first function');

echo $function;
```

```php
/**
 * This is the first function
 * 
 * @param  string|null  $name
 * @return void
 */
function sayHello(string|null $name = null): void
{
    echo 'Hello ' . $name;
}
```

### Create a simple class

In this example, a class is created and saved to a file:

```php
use Pop\Code\Generator;

// Create the class object and give it a namespace
$class = new Generator\ClassGenerator('MyClass');
$class->setNamespace(new Generator\NamespaceGenerator('MyApp'));

// Create a new protected property with a default value
$prop = new Generator\PropertyGenerator('foo', 'string', null, 'protected');

// Create a method and give it an argument, body and docblock description
$method = new Generator\MethodGenerator('setFoo', 'public');
$method->addArgument('foo', null, 'string')
    ->setBody('$this->foo = $foo;')
    ->addReturnType('void')
    ->setDesc('This is the method to set foo.');

// Add the property and the method to the class code object
$class->addProperty($prop);
$class->addMethod($method);

// Save the class to a file
$code = new Generator($class);
$code->writeToFile('MuClass.php');
```

The contents of the file will be:

```php
<?php

/**
 * @namespace 
 */
namespace MyApp;

class MyClass
{

    /**
     * @var   string
     */
    protected string|null $foo = null;

    /**
     * This is the method to set foo.
     * 
     * @param  string|null  $foo
     * @return void
     */
    public function setFoo(string|null $foo = null): void
    {
        $this->foo = $foo;
    }

}
```

[Top](#pop-code)

Generate Code
-------------

There are a number of individual code generators available to manage the creation and output of
various types of code blocks. Code generators are available for the following type of code:

- Classes
- Interfaces
- Traits
- Enums
- Methods
- Functions
- Constants
- Properties
- Namespaces
- Docblocks
- Attributes
- Bodies (general blocks of code)

### Create a file with some functions

```php
use Pop\Code\Generator;

$function1 = new Generator\FunctionGenerator('sayHello');
$function1->addArgument('name', null, 'string')
    ->setBody("echo 'Hello ' . \$name;")
    ->setDesc('This is the first function')
    ->addReturnType('void');

$function2 = new Generator\FunctionGenerator('sayGoodbye');
$function2->addArgument('name', null, 'string')
    ->setBody("echo 'Goodbye ' . \$name;")
    ->setDesc('This is the second function')
    ->addReturnType('void');

$code = new Generator();
$code->addCodeObjects([$function1, $function2]);
$code->writeToFile('functions.php');
```

The above code will produce a file called `functions.php` with the following
code in it:

```php
<?php

/**
 * This is the first function
 * 
 * @param  string|null  $name
 * @return void
 */
function sayHello(string|null $name = null): void
{
    echo 'Hello ' . $name;
}


/**
 * This is the second function
 * 
 * @param  string|null  $name
 * @return void
 */
function sayGoodbye(string|null $name = null): void
{
    echo 'Goodbye ' . $name;
}
```

[Top](#pop-code)

Enums
-----

Enums are generated much like classes, with cases added individually. A case's value must match the
enum's backing type — set no value at all for a pure (non-backed) enum.

```php
use Pop\Code\Generator;

$enum = new Generator\EnumGenerator('Status', 'string');
$enum->addCase(new Generator\EnumCaseGenerator('Active', 'active'));
$enum->addCase(new Generator\EnumCaseGenerator('Inactive', 'inactive'));

echo $enum;
```

```php
enum Status: string
{

    case Active = 'active';

    case Inactive = 'inactive';

}
```

An existing enum can be reflected the same way a class can, via `Reflection::createEnum()`:

```php
use Pop\Code\Reflection;

$enum = Reflection::createEnum('MyApp\Status');
```

[Top](#pop-code)

Attributes
----------

Attributes can be added to a class, interface, trait, enum, enum case, property, constant, method,
function, or an individual parameter. A class-level attribute renders on its own line with no indent;
a member-level one indents to match the member; a parameter-level one renders inline, before the
parameter's type.

```php
use Pop\Code\Generator;

$class = new Generator\ClassGenerator('Product');
$class->addAttribute(new Generator\AttributeGenerator('Entity'));

$table = new Generator\AttributeGenerator('Table');
$table->addArgument('products', 'name');
$class->addAttribute($table);

$prop = new Generator\PropertyGenerator('id', 'int');
$prop->addAttribute(new Generator\AttributeGenerator('Id'));
$class->addProperty($prop);

echo $class;
```

```php
#[Entity]
#[Table(name: 'products')]
class Product
{

    /**
     * @var   int
     */
    #[Id]
    public int|null $id = null;

}
```

`AttributeGenerator::addArgument(mixed $value, ?string $name = null)` takes the argument value first and
an optional name second — a positional argument is a single-argument call, and a named argument passes
its name as the second argument.

A parameter-level attribute is passed as the last argument to `addArgument()` on a
`FunctionGenerator`/`MethodGenerator`, as an array of one or more `AttributeGenerator` objects:

```php
use Pop\Code\Generator;

$function = new Generator\FunctionGenerator('handle');
$function->addArgument('request', new Generator\NoValue(), 'Request', false, false, [
    new Generator\AttributeGenerator('Autowire'),
]);
$function->setBody('return $request;');
$function->addReturnType('Request');

echo $function;
```

```php
/**
 * @param  Request|null  $request
 * @return Request
 */
function handle(#[Autowire] Request $request): Request
{
    return $request;
}
```

Reflecting existing code that carries attributes detects and reproduces them automatically — no extra
steps are needed beyond the usual `Reflection::createClass()` (or `createEnum()`, `createInterface()`,
etc.) call.

> **Note:** When an attribute class is referenced from a different namespace than the construct being
> reflected, a `use` import is only auto-generated for class-, interface-, trait-, and enum-level (and
> enum case-level) attributes — not for property, constant, method, function, or parameter attributes,
> since those reflectors have no access to the enclosing namespace. In that case the attribute renders
> with its short class name, which the consuming code is responsible for importing itself.

[Top](#pop-code)

Parse Code
----------

This `pop-code` component also provides the ability to parse existing code, which is useful
to obtain information about the code or to even modify and save new code.

In this example, we use the class that we created above. The reflection object provides
you with a code generator object like the one above so that you can add or remove things
from the parsed code.

```php
use Pop\Code\Reflection;
use Pop\Code\Generator;

$class = Reflection::createClass('MyApp\MyClass');

// Create the new method that you want to add to the existing class
$method = new Generator\MethodGenerator('hasFoo', 'public');
$method->addArgument('foo', null, 'string')
    ->setBody('return ($this->foo !== null);')
    ->setDesc('This is the method to see if foo is set.')
    ->addReturnType('bool');

// Access the generator and it's code object to add the new method to it
$class->addMethod($method);

// Echo out the code
$code = new Generator($class);
$code->writeToFile('MyClass.php');
```

And the modified class will look like, complete with the new `hasFoo()` method:

```php
<?php
/**
 * @namespace 
 */
namespace MyApp;

class MyClass
{

    /**
     * @var   string|null
     */
    protected string|null $foo = null;

    /**
     * This is the method to set foo.
     * 
     * @param  string|null  $foo
     * @return void
     */
    public function setFoo(string|null $foo = null): void
    {
        $this->foo = $foo;
        
    }

    /**
     * This is the method to see if foo is set.
     * 
     * @param  string|null  $foo
     * @return bool
     */
    public function hasFoo(string|null $foo = null): bool
    {
        return ($this->foo !== null);
    }

}
```

[Top](#pop-code)
