<?php

namespace Pop\Code\Test;

use Pop\Code\Generator;
use PHPUnit\Framework\TestCase;

class BodyGeneratorTest extends TestCase
{

    public function testAppendToBody()
    {
        $body = new Generator\BodyGenerator();
        $body->setBody("echo 'Hello!';");
        $this->assertEquals("        echo 'Hello!';", $body->getBody());
        $body->appendToBody("echo 'World!';");
        $this->assertEquals("        echo 'Hello!';" . PHP_EOL . "        echo 'World!';", $body->getBody());
        $this->assertTrue($body->hasBody());
    }

    public function testNegativeIndent()
    {
        $body = new Generator\BodyGenerator();
        $body->setIndent(0);
        $body->setBody("    echo 'Hello!';");
        $body->appendToBody("    echo 'World!';");
        $body->indentBody(-4);
        $this->assertEquals("    echo 'Hello!';" . PHP_EOL . "    echo 'World!';", $body->getBody());
    }

    public function testCreateReturnConfig1()
    {
        $config = [
            'database' => 'my_db',
            'username' => 'db_user',
            'password' => 'db_pass'
        ];

        $body = new Generator\BodyGenerator();
        $body->setIndent(0);
        $body->createReturnConfig($config);

        $render = (string)$body;

        $this->assertContains("return [", $render);
        $this->assertContains("'database' => 'my_db',", $render);
        $this->assertContains("'username' => 'db_user',", $render);
        $this->assertContains("'password' => 'db_pass',", $render);
        $this->assertContains("];", $render);
    }

    public function testCreateReturnConfig2()
    {
        $config = [
            'database' => 'my_db',
            'username' => 'db_user',
            'password' => 'db_pass'
        ];

        $body = new Generator\BodyGenerator();
        $body->setIndent(0);
        $body->createReturnConfig($config, 3);

        $render = (string)$body;

        $this->assertContains("return [", $render);
        $this->assertContains("'database' => 'my_db',", $render);
        $this->assertContains("'username' => 'db_user',", $render);
        $this->assertContains("'password' => 'db_pass',", $render);
        $this->assertContains("];", $render);
    }

}