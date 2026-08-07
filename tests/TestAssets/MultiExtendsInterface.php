<?php
/**
 * Fixture: an interface directly extending two interfaces, one of which (TestInterface) itself
 * extends a third (ParentInterface) -- guards against ParentInterface being incorrectly reported
 * as a direct parent of this interface too (native reflection's getInterfaces() returns the full
 * transitive closure, not just the direct extends list).
 */
namespace Pop\Code\Test\TestAssets;

interface MultiExtendsInterface extends TestInterface, ParentInterface2
{

}
