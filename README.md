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
        "popphp/pop-view" : "^6.0.0"
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

### Create an interface

An interface can extend one or more parent interfaces, passed as an array, a comma-separated string,
or added individually via `addParent()`/`addParents()`. Interface methods have no body — they always
render as a semicolon-terminated signature.

```php
use Pop\Code\Generator;

$interface = new Generator\InterfaceGenerator('Arrayable');
$interface->addMethod((new Generator\MethodGenerator('toArray'))->addReturnType('array'));

echo $interface;
```

```php
interface Arrayable
{

    /**
     * @return array
     */
    public function toArray(): array;

}
```

```php
$interface = new Generator\InterfaceGenerator('Countable2', ['Arrayable', 'Traversable']);

echo $interface;
```

```php
interface Countable2 extends Arrayable, Traversable
{

}
```

### Create a trait

A trait is built the same way as a class — properties, methods, constants — but has no `extends`,
`implements`, abstract/final flag, or readonly flag. Compose it into a class with `addUse()`:

```php
use Pop\Code\Generator;

$trait = new Generator\TraitGenerator('HasTimestamps');

$prop = new Generator\PropertyGenerator('createdAt', '\DateTime|null', null, 'protected');
$trait->addProperty($prop);

$touch = new Generator\MethodGenerator('touch', 'public');
$touch->setBody('$this->createdAt = new \DateTime();')->addReturnType('void');
$trait->addMethod($touch);

echo $trait;
```

```php
trait HasTimestamps
{

    /**
     * @var   \DateTime|null
     */
    protected \DateTime|null $createdAt = null;


    /**
     * @return void
     */
    public function touch(): void
    {
        $this->createdAt = new \DateTime();
    }
}
```

```php
$class = new Generator\ClassGenerator('Article');
$class->addUse('HasTimestamps');

echo $class;
```

```php
class Article
{

    use HasTimestamps;

}
```

> **Note:** `addUse($trait, $as)` on a `ClassGenerator`/`TraitGenerator`/`EnumGenerator` ignores `$as`
> — a whole-trait alias (`use SomeTrait as Alias;`) isn't valid PHP inside a class-like body (only
> per-method conflict resolution, which this simple API doesn't model, allows renaming). The `$as`
> alias *is* honored on `NamespaceGenerator::addUse()`, since a namespace-level `use Foo\Bar as Baz;`
> import is a different, valid construct — see [Namespaces and Imports](#namespaces-and-imports) below.

### Create a constant

`ConstantGenerator` can be used standalone or added to a class/interface/trait/enum via `addConstant()`.
A constant can optionally be rendered with a PHP 8.3+ typed-constant declaration via `setTyped()`, and
has its own visibility, independent of the value's type:

```php
use Pop\Code\Generator;

$class = new Generator\ClassGenerator('Retry');

$class->addConstant(new Generator\ConstantGenerator('MAX_RETRIES', 'int', 3));

$status = new Generator\ConstantGenerator('STATUS', 'string', 'active');
$status->setTyped(true)->setVisibility('protected');
$class->addConstant($status);

echo $class;
```

```php
class Retry
{

    /**
     * @var   int
     */
    public const MAX_RETRIES = 3;

    /**
     * @var   string
     */
    protected const string STATUS = 'active';

}
```

### Closures

`FunctionGenerator` can also render an anonymous function by passing `closure: true` to its
constructor. A closure with a `$name` renders as `$name = function(...) {...};`; one built with no
name at all (e.g. reflecting a real anonymous closure) renders as a bare `function(...) {...}`
expression:

```php
use Pop\Code\Generator;

$greet = new Generator\FunctionGenerator('greet', closure: true);
$greet->addArgument('name', null, 'string')->setBody("echo 'Hi ' . \$name;");

echo $greet;
```

```php
$greet = function(string|null $name = null)
{
    echo 'Hi ' . $name;
};
```

### Constructor property promotion

`MethodGenerator::addPromotedArgument($name, $visibility, $value, $type, $readonly, $attributes)` adds
a promoted constructor parameter — it's only valid on a method named `__construct`, and a `$readonly`
property requires a `$type`. A constructor whose body would otherwise be empty (all its work is the
promotion itself) needs an explicit `setBody('')` — without it, `MethodGenerator` treats "no body set"
the same as an abstract method and renders a semicolon-terminated stub instead of `{}`:

```php
use Pop\Code\Generator;

$class = new Generator\ClassGenerator('Point');

$ctor = new Generator\MethodGenerator('__construct');
$ctor->addPromotedArgument('x', 'public', new Generator\NoValue(), 'int')
    ->addPromotedArgument('y', 'public', new Generator\NoValue(), 'int')
    ->addPromotedArgument('label', 'private', new Generator\NoValue(), 'string', true)
    ->setBody('');

$class->addMethod($ctor);

echo $class;
```

```php
class Point
{

    /**
     * @param int     $x
     * @param int     $y
     * @param string  $label
     */
    public function __construct(public int $x, public int $y, private readonly string $label)
    {
        
    }

}
```

### Readonly properties and classes

`setAsReadonly()` is available on both `PropertyGenerator` and `ClassGenerator` (a readonly class,
PHP 8.2+, implicitly makes every declared property readonly):

```php
use Pop\Code\Generator;

$prop = new Generator\PropertyGenerator('id', 'int', null, 'public');
$prop->setAsReadonly();

echo $prop;
```

```php
    /**
     * @var   int
     */
    public readonly int $id;
```

```php
$class = new Generator\ClassGenerator('ImmutablePoint');
$class->setAsReadonly();

echo $class;
```

```php
readonly class ImmutablePoint
{

}
```

### Variadic and by-reference parameters

`addArgument($name, $value, $type, $variadic, $byRef, $attributes)` takes two extra positional flags
after `$type`. A variadic argument can't also have a default value:

```php
use Pop\Code\Generator;

$sum = new Generator\FunctionGenerator('sum');
$sum->addArgument('numbers', new Generator\NoValue(), 'int', true)
    ->setBody('return array_sum($numbers);')
    ->addReturnType('int');

echo $sum;
```

```php
function sum(int ...$numbers): int
{
    return array_sum($numbers);
}
```

```php
$inc = new Generator\FunctionGenerator('increment');
$inc->addArgument('counter', new Generator\NoValue(), 'int', false, true)
    ->setBody('$counter++;')
    ->addReturnType('void');

echo $inc;
```

```php
function increment(int &$counter): void
{
    $counter++;
}
```

### Union and intersection types

Any type string can be a union (`int|string`) or intersection (`\Countable&\Traversable`) — pass it
anywhere a type is accepted (parameter, return, property). A `null`-defaulted intersection type is
automatically wrapped in parens per PHP's DNF grammar (`(A&B)|null`, not the invalid bare `A&B|null`):

```php
use Pop\Code\Generator;

$fn = new Generator\FunctionGenerator('accept');
$fn->addArgument('value', new Generator\NoValue(), 'int|string')
    ->setBody('return $value;')
    ->addReturnType('int|string');

echo $fn;
```

```php
function accept(int|string $value): int|string
{
    return $value;
}
```

```php
$prop = new Generator\PropertyGenerator('collection', '\Countable&\Traversable', null, 'public');

echo $prop;
```

```php
    /**
     * @var   \Countable&\Traversable
     */
    public (\Countable&\Traversable)|null $collection = null;
```

### Abstract and final

`setAsAbstract()`/`setAsFinal()` are available on `ClassGenerator` and `MethodGenerator` (mutually
exclusive — setting one clears the other). An abstract method needs no body; it always renders as a
semicolon-terminated signature, the same as an interface method:

```php
use Pop\Code\Generator;

$class = new Generator\ClassGenerator('Shape');
$class->setAsAbstract(true);
$class->addMethod((new Generator\MethodGenerator('area', 'public'))->setAsAbstract(true)->addReturnType('float'));

echo $class;
```

```php
abstract class Shape
{

    /**
     * @return float
     */
    abstract public function area(): float;

}
```

### Special default values: `NoValue` and `Literal`

`addArgument()`/`addPromotedArgument()`'s `$value` parameter can't tell a real PHP `null` default
apart from "no default at all" using `null` alone — passing `null` always renders as an actual
`= null` default (and widens the type to include `null`). Two dedicated classes handle the other
cases:

- **`Generator\NoValue`** — a required parameter with no default whatsoever.
- **`Generator\Literal`** — a default that's a raw PHP expression (a constant/enum-case reference, a
  `new Foo()` call, etc.) to be emitted verbatim rather than quoted as a string value.

```php
use Pop\Code\Generator;

$fn = new Generator\FunctionGenerator('withDefault');
$fn->addArgument('mode', new Generator\Literal('self::DEFAULT_MODE'), 'string')
    ->setBody('return $mode;')
    ->addReturnType('string');

echo $fn;
```

```php
function withDefault(string $mode = self::DEFAULT_MODE): string
{
    return $mode;
}
```

### Docblocks

`DocblockGenerator` can be built and used directly — every generator that has one (`setDocblock()`)
accepts a manually-constructed instance instead of relying on the auto-generated `@param`/`@return`
tags:

```php
use Pop\Code\Generator;

$docblock = new Generator\DocblockGenerator('Adds two numbers together.');
$docblock->addParam('int', '$a', 'The first number');
$docblock->addParam('int', '$b', 'The second number');
$docblock->setReturn('int', 'The sum');
$docblock->setThrows('\InvalidArgumentException', 'If either number is negative');
$docblock->addTag('since', '5.0.0');

echo $docblock;
```

```php
    /**
     * Adds two numbers together.
     * 
     * @since  5.0.0
     * @param  int  $a The first number
     * @param  int  $b The second number
     * @throws \InvalidArgumentException If either number is negative
     * @return int The sum
     */
```

### General code bodies

`BodyGenerator` renders a standalone block of code with no surrounding function/class wrapper — useful
for a file that's just executable statements. It also has a `createReturnConfig()` helper that turns a
PHP array into a formatted `return [...]` statement, the common shape of a Pop PHP config file:

```php
use Pop\Code\Generator;

$body = new Generator\BodyGenerator();
$body->createReturnConfig([
    'debug'  => true,
    'routes' => [
        'home' => '/',
    ],
]);

$code = new Generator($body);
echo $code;
```

```php
<?php

    return [
        'debug' => true,
        'routes' => [
            'home' => '/',
        ],
    ];
```

### Namespaces and imports

`NamespaceGenerator::addUse($class, $as = null)` renders a namespace-level `use` import, aliased if
`$as` is given — this is the one `addUse()` where the alias is valid PHP and honored (see the note in
[Create a trait](#create-a-trait) above for why the class-, trait-, and enum-level `addUse()` ignore it):

```php
use Pop\Code\Generator;

$namespace = new Generator\NamespaceGenerator('App');
$namespace->addUse('App\Repository\UserRepository');
$namespace->addUse('App\Service\Mailer', 'AppMailer');

echo $namespace;
```

```php
/**
 * @namespace 
 */
namespace App;

use App\Repository\UserRepository;

use App\Service\Mailer as AppMailer;
```

A single `Generator` file container can also hold code objects across more than one namespace — pass a
namespace-keyed array (or an array of code objects per namespace) to `addCodeObjects()`, and each
namespace renders in its own `namespace X { ... }` block:

```php
use Pop\Code\Generator;

$code = new Generator();
$code->addCodeObjects([
    'App\One' => new Generator\ClassGenerator('Foo'),
    'App\Two' => new Generator\ClassGenerator('Bar'),
]);

echo $code;
```

```php
<?php
namespace App\One {
class Foo
{

}

}

namespace App\Two {
class Bar
{

}

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

An enum can also implement interfaces (third constructor argument, same array/comma-separated-string
handling as `ClassGenerator`) and carry constants and methods, exactly like a class:

```php
use Pop\Code\Generator;

$enum = new Generator\EnumGenerator('Status', 'string', 'HasLabel');
$enum->addCase(new Generator\EnumCaseGenerator('Active', 'active'));
$enum->addConstant(new Generator\ConstantGenerator('DEFAULT', 'string', 'active'));

$label = new Generator\MethodGenerator('label', 'public');
$label->addReturnType('string')->setBody('return match ($this) { self::Active => "Active" };');
$enum->addMethod($label);

echo $enum;
```

```php
enum Status: string implements HasLabel
{

    case Active = 'active';

    /**
     * @var   string
     */
    public const DEFAULT = 'active';

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) { self::Active => "Active" };
    }

}
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

### Reflecting other constructs

`Reflection::createClass()` and `Reflection::createEnum()` are two of ten static factory methods on the
`Reflection` facade — one per generator type, each accepting a class/object/name (or, for the smaller
constructs, an existing native `Reflector` or a value) and returning the matching `Generator\*` object:

| Facade method | Returns |
|---|---|
| `Reflection::createClass($class)` | `Generator\ClassGenerator` |
| `Reflection::createInterface($interface)` | `Generator\InterfaceGenerator` |
| `Reflection::createTrait($trait)` | `Generator\TraitGenerator` |
| `Reflection::createEnum($enum)` | `Generator\EnumGenerator` |
| `Reflection::createNamespace($namespace)` | `Generator\NamespaceGenerator` |
| `Reflection::createFunction($function)` | `Generator\FunctionGenerator` |
| `Reflection::createMethod($method)` | `Generator\MethodGenerator` |
| `Reflection::createProperty($property)` | `Generator\PropertyGenerator` |
| `Reflection::createConstant($constant)` | `Generator\ConstantGenerator` |
| `Reflection::createDocblock($docblock)` | `Generator\DocblockGenerator` |

```php
use Pop\Code\Reflection;

$trait     = Reflection::createTrait('MyApp\HasTimestamps');
$interface = Reflection::createInterface('MyApp\Arrayable');
$function  = Reflection::createFunction('array_map');
```

[Top](#pop-code)
