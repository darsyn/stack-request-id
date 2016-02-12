<?php

namespace DarsynTests\Stack\RequestId\Monolog;

use Darsyn\Stack\RequestId\Monolog\Processor as MonologProcessor;
use Symfony\Component\HttpFoundation\Request;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $processor;
    private $header = 'Foo-Id';

    public function setUp()
    {
        $this->processor = new MonologProcessor($this->header);
    }

    private function createGetResponseEvent($requestId = false)
    {
        $getResponseEventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $request = new Request;
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

    /**
     * @test
     */
    public function itAddsTheRequestIdIfItWasAvailableInTheRequest()
    {
        $record = array('message' => 'w00t w00t');
        $requestId = 'ea1379-42';
        $getResponseEvent = $this->createGetResponseEvent($requestId);
        $this->processor->onKernelRequest($getResponseEvent);
        $expectedRecord = $record;
        $expectedRecord['extra']['request_id'] = $requestId;
        $this->assertEquals($expectedRecord, $this->invokeProcessor($record));
    }

    /**
     * @test
     */
    public function itLeavesTheRecordUntouchedIfNoRequestIdWasAvailableInTheRequest()
    {
        $record = array('message' => 'w00t w00t');
        $getResponseEvent = $this->createGetResponseEvent();
        $this->processor->onKernelRequest($getResponseEvent);
        $expectedRecord = $record;
        $this->assertEquals($expectedRecord, $this->invokeProcessor($record));
    }
    /**
     * @test
     */
    public function itLeavesTheRecordUntouchedIfNoRequestWasHandled()
    {
        $record = array('message' => 'w00t w00t');
        $expectedRecord = $record;
        $this->assertEquals($expectedRecord, $this->invokeProcessor($record));
    }
}
