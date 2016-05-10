pop-code
========

[![Build Status](https://travis-ci.org/popphp/pop-code.svg?branch=master)](https://travis-ci.org/popphp/pop-code)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-code)](http://cc.popphp.org/pop-code/)

OVERVIEW
--------
`pop-code` provides the ability to dynamically generate PHP code on the fly
as well as parse and modify existing PHP code.

`pop-code` is a component of the [Pop PHP Framework](http://www.popphp.org/).

INSTALL
-------

Install `pop-code` using Composer.

    composer require popphp/pop-code

BASIC USAGE
-----------

### Create a class with a property and a method

```php
use Pop\Code\Generator;

// Create the class object and give it a namespace
$class = new Generator('MyClass.php', Generator::CREATE_CLASS);
$class->setNamespace(new Generator\NamespaceGenerator('MyApp'));

// Create a new protected property with a default value
$prop = new Generator\PropertyGenerator('foo', 'string', 'bar', 'protected');

// Create a method and give it an argument, body and docblock description
$method = new Generator\MethodGenerator('setFoo', 'public');
$method->addArgument('foo')
       ->setBody('$this->foo = $foo;')
       ->setDesc('This is the method to set foo.');

// Add the property and the method to the class code object
$class->code()->addProperty($prop);
$class->code()->addMethod($method);

// Save the class file
$class->save();

// Or, you can echo out the contents of the code directly
echo $class;
```

##### The newly created class will look like:

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
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;

    }

}
```

### Parse an existing class and add a method to it

In this example, we use the class that we created above. The reflection object provides
you with a code generator object like the one above so that you can add or remove things
from the parsed code.

```php
use Pop\Code\Generator;
use Pop\Code\Reflection;

$class = new Reflection('MyApp\MyClass');

// Create the new method that you want to add to the existing class
$method = new Generator\MethodGenerator('hasFoo', 'public');
$method->addArgument('foo')
       ->setBody('return (null !== $this->foo);')
       ->setDesc('This is the method to see if foo is set.');

// Access the generator and it's code object to add the new method to it
$reflect->generator()->code()->addMethod($method);

// Echo out the code
echo $reflect->generator();
```

##### The modified class will look like:

```php
<?php
/**
 * @namespace
 */
namespace MyApp;

class MyClass implements
{

    /**
     *
     * @var   string
     */
    protected $foo = 'bar';


    /**
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }


    /**
     * This is the method to see if foo is set.
     */
    public function hasFoo($foo)
    {
        return (null !== $this->foo);

    }

}
```

As you can see, the new method was appended to the class.
