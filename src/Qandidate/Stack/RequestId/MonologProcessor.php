<?php

/*
 * This file is part of the qandidate/stack-request-id package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qandidate\Stack\RequestId;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Processor to add the request id to monolog records.
 */
class MonologProcessor
{
    private $header;
    private $requestId;

    /**
     * @param string $header
     */
    public function __construct($header = 'X-Request-Id')
    {
        $this->header = $header;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request         = $event->getRequest();
        $this->requestId = $request->headers->get($this->header, false);
    }

    public function __invoke(array $record)
    {
        if ($this->requestId) {
            $record['extra']['request_id'] = $this->requestId;
        }

        return $record;
    }
}
