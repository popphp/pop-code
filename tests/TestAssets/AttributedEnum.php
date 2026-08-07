<?php
/**
 * Fixture: an attribute on the enum itself and on one of its cases.
 */
namespace Pop\Code\Test\TestAssets;

#[TagAttribute('enum')]
enum AttributedEnum: string
{

    #[TagAttribute('case')]
    case Active = 'active';
    case Inactive = 'inactive';

}
