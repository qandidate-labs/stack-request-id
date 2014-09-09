<?php

namespace Qandidate\Stack;

/**
 * Generates request ids.
 */
interface RequestIdGenerator
{
    /**
     * @return string
     */
    public function generate();
}
