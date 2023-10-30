pop-code
========

[![Build Status](https://github.com/popphp/pop-code/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-code/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-code)](http://cc.popphp.org/pop-code/)

[![Join the chat at https://popphp.slack.com](https://media.popphp.org/img/slack.svg)](https://popphp.slack.com)
[![Join the chat at https://discord.gg/D9JBxPa5](https://media.popphp.org/img/discord.svg)](https://discord.gg/D9JBxPa5)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Generate Code](#generate-code)
* [Parse Code](#parse-code)

Overview
--------
`pop-code` provides the ability to dynamically generate PHP code on the fly
as well as parse and modify existing PHP code.

`pop-code` is a component of the [Pop PHP Framework](http://www.popphp.org/).

Install
-------

Install `pop-code` using Composer.

    composer require popphp/pop-code

Or, require it in your composer.json file

    "require": {
        "popphp/pop-view" : "^5.0.0"
    }

[Top](#pop-code)

Quickstart
----------

### Create a simple function

In this example, a function is created and rendered to a string:

```php
use Pop\Code\Generator;

$function = new Generator\FunctionGenerator('sayHello');
$function->addArgument('name');
$function->setBody("echo 'Hello ' . \$name;");
$function->setDesc('This is the first function');

echo $function;
```

```php
/**
 * This is the first function
 * 
 * @param $name
 */
function sayHello($name)
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
$prop = new Generator\PropertyGenerator('foo', 'string', 'bar', 'protected');

// Create a method and give it an argument, body and docblock description
$method = new Generator\MethodGenerator('setFoo', 'public');
$method->addArgument('foo')
    ->setBody('$this->foo = $foo;')
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
    protected $foo = 'bar';

    /**
     * This is the method to set foo.
     * 
     * @param $foo
     */
    public function setFoo($foo)
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
- Methods
- Functions
- Constants
- Properties
- Namespaces
- Docblocks
- Bodies (general blocks of code)

### Create a file with some functions

```php
use Pop\Code\Generator;

$function1 = new Generator\FunctionGenerator('sayHello');
$function1->addArgument('name');
$function1->setBody("echo 'Hello ' . \$name;");
$function1->setDesc('This is the first function');

$function2 = new Generator\FunctionGenerator('sayGoodbye');
$function2->addArgument('name');
$function2->setBody("echo 'Goodbye ' . \$name;");
$function2->setDesc('This is the second function');

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
 * @param $name
 */
function sayHello($name)
{
    echo 'Hello ' . $name;
}

/**
 * This is the second function
 * 
 * @param $name
 */
function sayGoodbye($name)
{
    echo 'Goodbye ' . $name;
}
```

[Top](#pop-code)

Parse Code
----------

This `pop-code` component also provides the ability to parse existing code, which is useful
to obtain information about the code or to even modify and save new code from it.

In this example, we use the class that we created above. The reflection object provides
you with a code generator object like the one above so that you can add or remove things
from the parsed code.

```php
use Pop\Code\Reflection;
use Pop\Code\Generator;

$class = Reflection::createClass('MyApp\MyClass');

// Create the new method that you want to add to the existing class
$method = new Generator\MethodGenerator('hasFoo', 'public');
$method->addArgument('foo')
    ->setBody('return (null !== $this->foo);')
    ->setDesc('This is the method to see if foo is set.');

// Access the generator and it's code object to add the new method to it
$class->addMethod($method);

// Echo out the code
$code = new Generator($class);
$code->writeToFile('MyClass.php');
```

And the modified class will look like:

```php
<?php

/**
 * @namespace 
 */
namespace MyApp;

class MyClass
{

    /**
     * 
     * @var   string
     */
    protected $foo = 'bar';

    /**
     * @param $foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    /**
     * This is the method to see if foo is set.
     * 
     * @param $foo
     */
    public function hasFoo($foo)
    {
        return (null !== $this->foo);
    }

}
```

[Top](#pop-code)
