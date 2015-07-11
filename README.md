Pop Code
========

[![Build Status](https://travis-ci.org/popphp/pop-code.svg?branch=master)](https://travis-ci.org/popphp/pop-code)

OVERVIEW
--------
Pop Code is a component of the Pop PHP Framework 2. It provides the ability to dynamically generate
PHP code on the fly as well as parse and modify existing PHP code.

INSTALL
-------

Install `Pop Code` using Composer.

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

Echoing the $class object out will result in:

```php
<?php
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