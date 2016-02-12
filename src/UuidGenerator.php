<?php

namespace Darsyn\Stack\RequestId;

use Ramsey\Uuid\Uuid;

class UuidGenerator implements GeneratorInterface
{
    private $nodeId;

    /**
     * @param scalar $nodeId
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
