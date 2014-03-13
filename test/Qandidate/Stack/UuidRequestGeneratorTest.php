<?php

namespace Qandidate\Stack;

class UuidRequestIdGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_a_string()
    {
        $generator = new UuidRequestIdGenerator();

        $this->assertInternalType('string', $generator->generate());
    }
}
