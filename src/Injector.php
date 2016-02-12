<?php

namespace Darsyn\Stack\RequestId;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Injector implements HttpKernelInterface
{
    /**
     * @access private
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $app;

    /**
     * @access private
     * @var \Darsyn\Stack\RequestId\GeneratorInterface
     */
    private $generator;

    /**
     * @access private
     * @var string
     */
    private $header;

    /**
     * @access private
     * @var boolean
     */
    private $respond;

    /**
     * Constructor
     *
     * @access public
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
     * @param \Darsyn\Stack\RequestId\GeneratorInterface $generator
     * @param string $header
     * @param boolean $respond
     */
    public function __construct(
        HttpKernelInterface $app,
        GeneratorInterface $generator,
        $header = 'X-Request-Id',
        $respond = true
    ) {
        $this->app       = $app;
        $this->generator = $generator;
        $this->header    = (string) $header;
        $this->respond   = (bool) $respond;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        // Unlike Qandidate's implementation, we do not want the client to set the value of the header.
        // It would just be more user input to filter.
        $request->headers->set($this->header, $this->generator->generate());
        $response = $this->app->handle($request, $type, $catch);
        // Set the request ID header in the response if specified.
        if ($this->respond) {
            $response->headers->set($this->header, $request->headers->get($this->header));
        }
        return $response;
    }
}
