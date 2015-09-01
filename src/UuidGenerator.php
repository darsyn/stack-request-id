<?php
namespace Darsyn\Stack\RequestId;

use Rhumsaa\Uuid\Uuid;

class UuidGenerator implements GeneratorInterface
{
    private $nodeId;

    /**
     * @param null|string|integer $nodeId
     */
    public function __construct($nodeId = null)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        return Uuid::uuid1($this->nodeId)->toString();
    }
}
