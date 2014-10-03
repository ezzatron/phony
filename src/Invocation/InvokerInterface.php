<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Invocation;

use Exception;

/**
 * The interface implemented by invokers.
 */
interface InvokerInterface
{
    /**
     * Calls a callback, maintaining reference parameters.
     *
     * @param callable                  $callback  The callback.
     * @param array<integer,mixed>|null $arguments The arguments.
     *
     * @return mixed     The result of invocation.
     * @throws Exception If an error occurs.
     */
    public function callWith($callback, array $arguments = null);
}