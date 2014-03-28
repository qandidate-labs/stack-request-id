<?php

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
