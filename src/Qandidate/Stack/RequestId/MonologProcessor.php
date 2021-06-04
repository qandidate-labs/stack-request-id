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

namespace Qandidate\Stack\RequestId;

use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Processor to add the request id to monolog records.
 */
class MonologProcessor
{
    /** @var string */
    private $header;

    /** @var string|null */
    private $requestId;

    public function __construct(string $header = 'X-Request-Id')
    {
        $this->header = $header;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $this->requestId = (string) $request->headers->get($this->header, '');
    }

    public function __invoke(array $record): array
    {
        if ($this->requestId) {
            $record['extra']['request_id'] = $this->requestId;
        }

        return $record;
    }
}
