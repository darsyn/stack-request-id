<?php
namespace Darsyn\Stack\RequestId\Monolog;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class Processor
{
    /**
     * @access private
     * @var string
     */
    private $header;

    /**
     * @access private
     * @var string
     */
    private $requestId;

    /**
     * Constructor
     *
     * @access public
     * @param string $header
     */
    public function __construct($header = 'X-Request-Id')
    {
        $this->header = $header;
    }

    /**
     * Event: On Kernel Request
     *
     * @access public
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request         = $event->getRequest();
        $this->requestId = $request->headers->get($this->header, false);
    }

    /**
     * Magic Method: Invoke
     *
     * @access public
     * @param array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        if ($this->requestId) {
            $record['extra']['request_id'] = $this->requestId;
        }
        return $record;
    }
}
