<?php

declare(strict_types=1);

/*
 * This file is part of the qandidate/stack-request-id package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qandidate\Stack;

use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

/**
 * Generates a uuid for the request id.
 */
class UuidRequestIdGenerator implements RequestIdGenerator
{
    /** @var mixed */
    private $nodeId;

    /**
     * @param Hexadecimal|int|string|null $nodeId
     */
    public function __construct($nodeId = null)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(): string
    {
        return Uuid::uuid1($this->nodeId)->toString();
    }
}
