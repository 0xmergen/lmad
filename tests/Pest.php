<?php

declare(strict_types=1);

use Lmad\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test function is bound to a PHPUnit test
| case class. A "TestCase" class is included with the package allowing you
| to extend the base test case if required.
|
*/

uses(TestCase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests it's often helpful to check that a value or
| values match certain expectations. Pest provides a number of expectations
| that you can use to make assertions.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out of the box, you may have some testing code
| specific to your project that you don't want to repeat in every file.
| Here you can also expose helpers as global functions to help you to reduce
| the number of lines of code in your test files.
|
*/
