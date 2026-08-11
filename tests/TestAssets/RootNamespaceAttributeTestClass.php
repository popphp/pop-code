<?php
/**
 * Fixture: a root-namespace (global) attribute, to guard against AttributeCollector
 * reducing it to an unqualified short name that would resolve incorrectly once
 * regenerated inside a namespaced construct (PHP resolves attribute classes lazily,
 * so a wrong resolution produces no error at load time -- only when something
 * actually asks for the attribute instance).
 */
namespace Pop\Code\Test\TestAssets;

#[\AllowDynamicProperties]
class RootNamespaceAttributeTestClass
{
}
