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

use LogicException;

/**
 * Generates a uuid for the request id.
 */
class UuidRequestIdGenerator implements RequestIdGenerator
{
    private $nodeId;

    private $className;

    /**
     * @param null|string|integer $nodeId
     * @param null|string         $className
     */
    public function __construct($nodeId = null)
    {
        $this->nodeId = $nodeId;
        $this->className = $this->getClassName();
    }

    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        return call_user_func([$this->className, 'uuid1'], $this->nodeId)->toString();
    }

    /**
     * @return string
     */
    private function getClassName()
    {
        if (class_exists('Ramsey\Uuid\Uuid')) {
            return '\Ramsey\Uuid\Uuid';
        }

        if (class_exists('Rhumsaa\Uuid\Uuid')) {
            return '\Rhumsaa\Uuid\Uuid';
        }

        throw new LogicException('UuidRequestIdGenerator requires library ramsey/uuid.');
    }
}
