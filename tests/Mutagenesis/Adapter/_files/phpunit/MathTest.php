<?php

require_once __DIR__ . '/Math.php';

class MM1_MathTest extends PHPUnit_Framework_TestCase
{

    /**
     * @group PHPUnitRunnerTesting
     */
    public function testAdds()
    {
        $math = new Phpunit_MM1_Math;
        $this->assertEquals(4, $math->add(2,2));
    }
}
