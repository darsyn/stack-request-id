<?php

namespace DarsynTests\Stack\RequestId;

use Darsyn\Stack\RequestId\Injector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class InjectorTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $requestIdGenerator;
    private $stackedApp;
    private $header = 'X-Request-Id';

    public function setUp()
    {
        $this->requestIdGenerator = $this->getMock('Darsyn\Stack\RequestId\UuidGenerator');
        $this->app                = new MockApp($this->header);
        $this->stackedApp         = new Injector($this->app, $this->requestIdGenerator, $this->header);
    }

    private function createRequest($requestId = null)
    {
        $request = new Request;
        if ($requestId) {
            $request->headers->set($this->header, $requestId);
        }
        return $request;
    }

    /**
     * @test
     */
    public function itCallsTheGeneratorWhenNoRequestIdIsPresent()
    {
        $this->requestIdGenerator
            ->expects($this->once())
            ->method('generate');
        $this->stackedApp->handle($this->createRequest());
    }

    /**
     * @test
     */
    public function itSetsTheRequestIdInTheHeader()
    {
        $this->requestIdGenerator
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('yolo'));
        $this->stackedApp->handle($this->createRequest());
        $this->assertEquals('yolo', $this->app->getLastHeaderValue());
    }

    /**
     * @test
     */
    public function itSetsTheRequestIdInTheResponseHeaderIfEnabled()
    {
        $this->requestIdGenerator
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('yolo'));
        $response = $this->stackedApp->handle($this->createRequest());
        $this->assertSame('yolo', $response->headers->get($this->header));
    }

    /**
     * @test
     */
    public function itSetsTheRequestIdInACustomResponseHeaderIfGiven()
    {
        $stackedApp = new Injector($this->app, $this->requestIdGenerator, 'Request-Id');
        $this->requestIdGenerator
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('yolo'));
        $response = $stackedApp->handle($this->createRequest());
        $this->assertSame('yolo', $response->headers->get('Request-Id'));
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
        return new Response;
    }

    public function getLastHeaderValue()
    {
        return $this->headerValue;
    }
}
