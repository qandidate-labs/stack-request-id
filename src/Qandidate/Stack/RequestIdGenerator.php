<?php

namespace Qandidate\Stack;

/**
 * Generates request ids.
 */
abstract class RequestIdGenerator
{
    /**
     * @return string
     */
    abstract public function generate();
}
