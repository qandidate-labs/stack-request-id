<?php

namespace Qandidate\Stack;

use Rhumsaa\Uuid\Uuid;

/**
 * Generates a uuid for the request id.
 */
class UuidRequestIdGenerator extends RequestIdGenerator
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
