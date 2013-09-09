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

class Mutagenesis_Renderer_TextTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->_renderer = new \Mutagenesis\Renderer\Text;
    }
    
    public function testRendersOpeningMessage()
    {
        $this->assertEquals(
            'Mutagenesis: Mutation Testing for PHP' . PHP_EOL . PHP_EOL,
            $this->_renderer->renderOpening()
        );
    }
    
    public function testRendersFailMessageIfTestSuiteDidNotPassDuringPretest()
    {
        $result = false;
        $testOutput = 'Stuff failed';
        $this->assertEquals(
            'Before you face the Mutants, you first need a 100% pass rate!'
                . PHP_EOL
                . 'That means no failures or errors (we\'ll allow skipped or incomplete tests).'
                . PHP_EOL . PHP_EOL
                . $testOutput
                . PHP_EOL . PHP_EOL,
            $this->_renderer->renderPretest($result, $testOutput)
        );
    }
    
    public function testRendersPassMessageIfTestSuiteDidPassDuringPretest()
    {
        $result = true;
        $testOutput = 'Stuff passed';
        $this->assertEquals(
            'All initial checks successful! The mutagenic slime has been activated.'
                . PHP_EOL . PHP_EOL
                . '    > ' . $testOutput
                . PHP_EOL . PHP_EOL . 'Stand by...Mutation Testing commencing.'
                . PHP_EOL . PHP_EOL,
            $this->_renderer->renderPretest($result, $testOutput)
        );
    }
    
    public function testRendersProgressMarkAsPeriodCharacterIfTestResultWasFalse()
    {
        $this->assertEquals('.', $this->_renderer->renderProgressMark(false));
    }
    
    public function testRendersProgressMarkAsECharacterIfTestResultWasFalse()
    {
        $this->assertEquals('E', $this->_renderer->renderProgressMark(true));
    }
    
    public function testRendersFinalReportWithNoEscapeesFromASingleMutant()
    {
        $this->assertEquals(
            PHP_EOL . PHP_EOL
                . '1 Mutant born out of the mutagenic slime!'
                . PHP_EOL . PHP_EOL
                . 'No Mutants survived! Someone in QA will be happy.'
                . PHP_EOL . PHP_EOL,
            $this->_renderer->renderReport(1, 1, 0, array(), array(), '')
        );   
    }
    
    public function testRendersFinalReportWithEscapeesFromASingleMutant()
    {
        $escaped = $this->getMock(
            'Mutagenesis\\Mutation\\BooleanTrue',
            array('getDiff'),
            array(),
            'MockBooleanTrue',
            false
        );
        $escaped->expects($this->once())
            ->method('getDiff')
            ->will($this->returnValue('diff1'));
        $expected = <<<EXPECTED


1 Mutant born out of the mutagenic slime!

1 Mutant escaped; the integrity of your source code may be compromised by the following Mutants:

1)
Difference on Foo::bar() in /path/to/foo.php
===================================================================
diff1
    > test1output

Happy Hunting! Remember that some Mutants may just be Ghosts (or if you want to be boring, 'false positives').


EXPECTED;
        $mutations = array(
            array(
                'class' => 'Foo',
                'method' => 'bar',
                'file' => '/path/to/foo.php',
                'mutation' => $escaped
            )
        );
        $this->assertEquals(
            $expected,
            $this->_renderer->renderReport(1, 0, 1, $mutations, array(), 'test1output')
        );   
    }

}
