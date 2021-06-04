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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Middleware adding a unique request id to the request if it is not present.
 */
class RequestId implements HttpKernelInterface
{
    /** @var HttpKernelInterface */
    private $app;

    /** @var RequestIdGenerator */
    private $generator;

    /** @var string */
    private $header;

    /** @var string|null */
    private $responseHeader;

    public function __construct(
        HttpKernelInterface $app,
        RequestIdGenerator $generator,
        string $header = 'X-Request-Id',
        ?string $responseHeader = null
    ) {
        $this->app = $app;
        $this->generator = $generator;
        $this->header = $header;
        $this->responseHeader = $responseHeader;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true)
    {
        if (!$request->headers->has($this->header)) {
            $request->headers->set($this->header, $this->generator->generate());
        }

        $response = $this->app->handle($request, $type, $catch);

        if (null !== $this->responseHeader) {
            $response->headers->set($this->responseHeader, (string) $request->headers->get($this->header, ''));
        }

        return $response;
    }

    public function enableResponseHeader(string $header = 'X-Request-Id'): void
    {
        $this->responseHeader = $header;
    }
}
