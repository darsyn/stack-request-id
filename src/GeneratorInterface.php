<?php

namespace Darsyn\Stack\RequestId;

interface GeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
