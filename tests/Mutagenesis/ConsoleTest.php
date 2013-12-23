<?php
/**
 * Mutagenesis
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mutateme/blob/rewrite/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mutagenesis
 * @package    Mutagenesis
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

namespace MutagenesisTest;

use Mutagenesis\Console;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->root = __DIR__ . '/_files/root/base1';
    }

    public function testConsoleSetsRunnerBaseDirectoryFromCommandLineOptions()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        Console::main(array('base'=>$this->root), $runner);
        $this->assertEquals($this->root, $runner->getBaseDirectory());
    }

    public function testConsoleSetsRunnerSourceDirectoryFromCommandLineOptions()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        Console::main(array('src'=>$this->root . '/library'), $runner);
        $this->assertEquals($this->root . '/library', $runner->getSourceDirectory());
    }

    public function testConsoleSetsRunnerSourceExcludesFromCommandLineOptions()
    {
        $runner = $this->getMock('Mutagenesis\Runner\Base', array('execute'));
        Console::main(array('src-exclude' => array("*bool*.php", "*Dave.php")), $runner);
        $this->assertEquals(array("*bool*.php", "*Dave.php"), $runner->getSourceExcludes());
    }

    public function testConsoleSetsRunnerTestsDirectoryFromCommandLineOptions()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        Console::main(array('tests'=>$this->root . '/tests'), $runner);
        $this->assertEquals($this->root . '/tests', $runner->getTestDirectory());
    }

    public function testConsoleSetsRunnerAdapterNameFromCommandLineOptions()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        Console::main(array('adapter'=>'foobar'), $runner);
        $this->assertEquals('foobar', $runner->getAdapterName());
    }

    public function testConsoleSetsRunnerAdapterOptionStringFromCommandLineOptions()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        Console::main(array('options'=>'foobar'), $runner);
        $this->assertEquals(array('foobar'), $runner->getAdapterOptions());
    }

    public function testConsoleSetsRunnerAdapterToPhpunitByDefault()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        Console::main(array(), $runner);
        $this->assertEquals('phpunit', $runner->getAdapterName());
    }

    public function testConsoleSetsRunnerAdapterOptionsToEmptyStringByDefault()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        Console::main(array(), $runner);
        $this->assertEquals(array(), $runner->getAdapterOptions());
    }

    public function testConsoleExecutesRunnerAndEchosOutput()
    {
        $runner = $this->getMock('\Mutagenesis\Runner\Base', array('execute'));
        $runner->expects($this->once())->method('execute')->will($this->returnValue('mutation results'));
        ob_start();
        Console::main(null, $runner);
        $this->assertEquals(ob_get_clean(), 'mutation results');
    }

}
