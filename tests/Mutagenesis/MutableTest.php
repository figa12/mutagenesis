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

class Mutagenesis_MutableTest extends PHPUnit_Framework_TestCase
{

    protected $root = '';

    public function setUp()
    {
        $this->root = __DIR__ . '/_files/root/base2/library';
    }

    public function testShouldMaintainFilePathInfoOncePassedInConstructor()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/foo.php');
        $this->assertEquals($this->root . '/foo.php', $file->getFilename());
    }

    public function testShouldNotHaveMutationsBeforeGeneration()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math1.php');
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldNotHaveDetectedMutablesBeforeGeneration()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math1.php');
        $this->assertEquals(array(), $file->getMutables());
    }

    public function testShouldNotGenerateMutablesForEmptyClass()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math0.php');
        $file->generate();
        $this->assertEquals(array(), $file->getMutables());
    }

    public function testShouldNotgenerateForEmptyClass()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math0.php');
        $file->generate();
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldNotGenerateMutationsIfOnlyEmptyMethodsInClass()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math00.php');
        $file->generate();
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldGenerateMutablesEvenIfMethodBodyIsNotViable()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math000.php');
        $file->generate();
        $return = $file->getMutables();
        $this->assertEquals(array('file','class','method','args','tokens'),array_keys($return[0]));
    }

    public function testShouldNotGenerateMutablesIfMethodBodyIsNotViable()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math000.php');
        $file->generate();
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldGenerateAMutationIfPossible()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math1.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertEquals(array('file','class','method','args','tokens','index','mutation'),array_keys($return[0]));
    }

    public function testShouldReturnMutationsAsMutantObjectWrappers()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math1.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertTrue($return[0]['mutation'] instanceof \Mutagenesis\Mutation\MutationAbstract);
    }

    public function testShouldDetectMutablesForClassesInSameFileSeparately()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/mathx2.php');
        $file->generate();
        $return = $file->getMutables();
        $this->assertEquals('Math2', $return[1]['class']);
    }

    public function testShouldDetectMutationsForClassesInSameFileSeparately()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/mathx2.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertEquals('Math2', $return[1]['class']);
    }


    // Ensure correct class is returned as a mutation


    public function testShouldGenerateAdditionOperatorMutationWhenPlusSignDetected()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math1.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertTrue($return[0]['mutation'] instanceof \Mutagenesis\Mutation\OperatorAddition);
    }

    public function testShouldGenerateSubtractionOperatorMutationWhenMinusSignDetected()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math2.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertTrue($return[0]['mutation'] instanceof \Mutagenesis\Mutation\OperatorSubtraction);
    }

    public function testShouldGenerateIncrementOperatorMutationWhenPostIncrementDetected()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math3.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertTrue($return[0]['mutation'] instanceof \Mutagenesis\Mutation\OperatorIncrement);
    }

    public function testShouldGenerateIncrementOperatorMutationWhenPreIncrementDetected()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/math4.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertTrue($return[0]['mutation'] instanceof \Mutagenesis\Mutation\OperatorIncrement);
    }

    public function testShouldGenerateBooleanTrueMutationWhenBoolTrueDetected()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/bool1.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertTrue($return[0]['mutation'] instanceof \Mutagenesis\Mutation\BooleanTrue);
    }

    public function testShouldGenerateBooleanFalseMutationWhenBoolFalseDetected()
    {
        $file = new \Mutagenesis\Mutable($this->root . '/bool2.php');
        $file->generate();
        $return = $file->getMutations();
        $this->assertTrue($return[0]['mutation'] instanceof \Mutagenesis\Mutation\BooleanFalse);
    }
    
    /**
     * Covers bug where Mutable may incorrectly parse a method and omit the first
     * opening bracket in an IF clause, leading to syntax errors when
     * attempting to add the new method block via runkit
     *
     * @group MM1
     */
    public function testCreatesAccurateMapOfIfClausesSingleNonStaticMethod()
    {
        $file = new \Mutagenesis\Mutable(__DIR__ . '/_files/IfClause.php');
        $file->generate();
        $mutations = $file->getMutations();
        $mutation = $mutations[0];
        $this->assertEquals(__DIR__ . '/_files/IfClause.php', $mutation['file']);
        $this->assertEquals('Some_Class_With_If_Clause_In_Method', $mutation['class']);
        $this->assertEquals('_getSession', $mutation['method']);
        $this->assertEquals('', $mutation['args']);
        $block = <<<BLOCK

        static \$session = null;
        if (\$session === null) {
            \$session = new Zend_Session_Namespace(
                \$this->getSessionNamespace(), true
            );
        }
    
BLOCK;
        $this->assertEquals($block, $this->_reconstructFromTokens($mutation['tokens']));
    }
    
    /**
     * Reconstruct a string of source code from its constituent tokens
     *
     * @param array $tokens
     * @return string
     */
    protected function _reconstructFromTokens(array $tokens)
    {
        $str = '';
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $str .= $token;
            } else {
                $str .= $token[1];
            }
        }
        return $str;
    }

}
