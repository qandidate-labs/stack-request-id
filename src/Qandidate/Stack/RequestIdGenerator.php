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

/**
 * Generates request ids.
 */
interface RequestIdGenerator
{
    public function generate(): string;
}
