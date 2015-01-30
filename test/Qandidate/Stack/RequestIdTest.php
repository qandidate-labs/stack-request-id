<?php

/*
 * This file is part of the qandidate/stack-request-id package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qandidate\Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestIdTest extends TestCase
{
    private $app;
    private $requestIdGenerator;
    private $stackedApp;
    private $header = 'X-Request-Id';

    public function setUp()
    {
        $this->requestIdGenerator = $this->getMock('Qandidate\Stack\RequestIdGenerator');
        $this->app                = new MockApp($this->header);
        $this->stackedApp         = new RequestId($this->app, $this->requestIdGenerator, $this->header);
    }

    /**
     * @test
     */
    public function it_calls_the_generator_when_no_request_id_is_present()
    {
        $this->requestIdGenerator->expects($this->once())
            ->method('generate');

        $this->stackedApp->handle($this->createRequest());
    }

    /**
     * @test
     */
    public function it_sets_the_request_id_in_the_header()
    {
        $this->requestIdGenerator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('yolo'));

        $this->stackedApp->handle($this->createRequest());

        $this->assertEquals('yolo', $this->app->getLastHeaderValue());
    }

    /**
     * @test
     */
    public function it_does_not_set_a_new_request_id_if_it_was_already_present()
    {
        $this->requestIdGenerator->expects($this->never())
            ->method('generate');

        $this->stackedApp->handle($this->createRequest('foo'));

        $this->assertEquals('foo', $this->app->getLastHeaderValue());
    }

    /**
     * @test
     */
    public function it_sets_the_request_id_in_the_response_header_if_enabled()
    {
        $this->stackedApp->enableResponseHeader();

        $this->requestIdGenerator->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('yolo'));

        $response = $this->stackedApp->handle($this->createRequest());

        $this->assertSame('yolo', $response->headers->get($this->header));
    }

    /**
     * @test
     */
    public function it_sets_the_request_id_in_a_custom_response_header_if_given()
    {
        $this->stackedApp->enableResponseHeader('Request-Id');

        $this->requestIdGenerator->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('yolo'));

        $response = $this->stackedApp->handle($this->createRequest());

        $this->assertSame('yolo', $response->headers->get('Request-Id'));
    }

    /**
     * @test
     */
    public function it_does_not_set_the_request_id_in_the_response_header_by_default()
    {
        $this->requestIdGenerator->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('yolo'));

        $response = $this->stackedApp->handle($this->createRequest());

        $this->assertFalse($response->headers->has($this->header), 'The request id is not added to the response by default');
    }

    private function createRequest($requestId = null)
    {
        $request  = new Request();

        if ($requestId) {
            $request->headers->set($this->header, $requestId);
        }

        return $request;
    }

}

class MockApp implements HttpKernelInterface
{
    private $headerValue;
    private $recordHeader;

    public function __construct($recordHeader)
    {
        $this->recordHeader = $recordHeader;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $this->headerValue = $request->headers->get($this->recordHeader);

        return new Response();
    }

    public function getLastHeaderValue()
    {
        return $this->headerValue;
    }
}
