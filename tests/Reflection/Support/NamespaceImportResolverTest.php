<?php

namespace Pop\Code\Test\Reflection\Support;

use Pop\Code\Reflection\Support\NamespaceImportResolver;
use PHPUnit\Framework\TestCase;

class NamespaceImportResolverTest extends TestCase
{

    public function testSameNamespaceAsTheReflectedConstructNeedsNoImport()
    {
        $resolver = new NamespaceImportResolver();
        [$reference, $needsImport] = $resolver->resolve('My\App\Foo', 'My\App');

        $this->assertEquals('Foo', $reference);
        $this->assertFalse($needsImport);
    }

    public function testForeignNamespaceFirstClaimNeedsAnImport()
    {
        $resolver = new NamespaceImportResolver();
        [$reference, $needsImport] = $resolver->resolve('Other\Ns\Foo', 'My\App');

        $this->assertEquals('Foo', $reference);
        $this->assertTrue($needsImport);
    }

    public function testResolvingTheSameFqcnAgainDoesNotNeedASecondImport()
    {
        $resolver = new NamespaceImportResolver();
        $resolver->resolve('Other\Ns\Foo', 'My\App');
        [$reference, $needsImport] = $resolver->resolve('Other\Ns\Foo', 'My\App');

        $this->assertEquals('Foo', $reference);
        $this->assertFalse($needsImport);
    }

    public function testDifferentNamespacesWithTheSameShortNameCollideAndFallBackToFqcn()
    {
        $resolver = new NamespaceImportResolver();
        [$firstReference, $firstNeedsImport]   = $resolver->resolve('NsA\Tag', 'My\App');
        [$secondReference, $secondNeedsImport] = $resolver->resolve('NsB\Tag', 'My\App');

        $this->assertEquals('Tag', $firstReference);
        $this->assertTrue($firstNeedsImport);

        $this->assertEquals('\NsB\Tag', $secondReference);
        $this->assertFalse($secondNeedsImport);
    }

    public function testRootNamespaceFqcnAlwaysUsesALeadingBackslashWithNoImport()
    {
        // A genuinely root-namespace class (no backslash at all in its FQCN) is always
        // unambiguous with a leading backslash, regardless of what namespace it's referenced
        // from -- it never needs (or benefits from) a `use` import, and can never collide with
        // one, unlike a namespaced class whose bare short name could shadow another import.
        $resolver = new NamespaceImportResolver();
        [$reference, $needsImport] = $resolver->resolve('Foo', 'My\App');

        $this->assertEquals('\Foo', $reference);
        $this->assertFalse($needsImport);
    }

    public function testRootNamespaceFqcnNeverCollidesWithAClaimedShortName()
    {
        $resolver = new NamespaceImportResolver();
        $resolver->resolve('NsA\Tag', 'My\App');
        [$reference, $needsImport] = $resolver->resolve('Tag', 'My\App');

        $this->assertEquals('\Tag', $reference);
        $this->assertFalse($needsImport);
    }

}
