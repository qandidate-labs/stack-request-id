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

/**
 * Generates a uuid for the request id.
 */
class UuidRequestIdGenerator implements RequestIdGenerator
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
        if (class_exists('Ramsey\Uuid\Uuid')) {
            return \Ramsey\Uuid\Uuid::uuid1($this->nodeId)->toString();
        } elseif (class_exists('Rhumsaa\Uuid\Uuid')) {
            return \Rhumsaa\Uuid\Uuid::uuid1($this->nodeId)->toString();
        } else {
            throw new \LogicException('UuidRequestIdGenerator requires library ramsey/uuid.');
        }
    }
}
