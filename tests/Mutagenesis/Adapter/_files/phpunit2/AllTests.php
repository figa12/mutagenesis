<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Mutagenesis_Test2_AllTests::main');
}

require_once __DIR__ . '/MathTest.php';

class Mutagenesis_Test2_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Math');

        $suite->addTestSuite('MM2_MathTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Mutagenesis_Test2_AllTests::main') {
    Mutagenesis_Test2_AllTests::main();
}
