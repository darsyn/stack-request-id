<?php

namespace DarsynTests\Stack\RequestId;

use Darsyn\Stack\RequestId\UuidGenerator;

class UuidGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itGeneratesAString()
    {
        $generator = new UuidGenerator;
        $this->assertInternalType('string', $generator->generate());
    }
}
