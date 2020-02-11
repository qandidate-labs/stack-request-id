<?php

/*
 * This file is part of the qandidate/stack-request-id package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qandidate\Stack\RequestId;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class MonologProcessorTest extends TestCase
{
    private $processor;
    private $header = 'Foo-Id';

    public function setUp(): void
    {
        $this->processor = new MonologProcessor($this->header);
    }

    /**
     * @test
     */
    public function it_adds_the_request_id_if_it_was_available_in_the_request()
    {
        $record           = array('message' => 'w00t w00t');
        $requestId        = 'ea1379-42';
        $getResponseEvent = $this->createGetResponseEvent($requestId);

        $this->processor->onKernelRequest($getResponseEvent);

        $expectedRecord = $record;
        $expectedRecord['extra']['request_id'] = $requestId;

        $this->assertEquals($expectedRecord, $this->invokeProcessor($record));
    }

    /**
     * @test
     */
    public function it_leaves_the_record_untouched_if_no_request_id_was_available_in_the_request()
    {
        $record           = array('message' => 'w00t w00t');
        $getResponseEvent = $this->createGetResponseEvent();

        $this->processor->onKernelRequest($getResponseEvent);

        $expectedRecord = $record;

        $this->assertEquals($expectedRecord, $this->invokeProcessor($record));
    }
    /**
     * @test
     */
    public function it_leaves_the_record_untouched_if_no_request_was_handled()
    {
        $record = array('message' => 'w00t w00t');

        $expectedRecord = $record;

        $this->assertEquals($expectedRecord, $this->invokeProcessor($record));
    }

    private function createGetResponseEvent($requestId = false)
    {
        $getResponseEventMock = $this->createMock('Symfony\Component\HttpKernel\Event\GetResponseEvent');

        $request = new Request();

        if (false !== $requestId) {
            $request->headers->set($this->header, $requestId);
        }

        $getResponseEventMock
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $getResponseEventMock;
    }

    private function invokeProcessor(array $record)
    {
        return call_user_func_array($this->processor, array($record));
    }
}
