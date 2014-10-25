<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Hash;

/**
 * The interface implemented by hash generators.
 */
interface HashGeneratorInterface
{
    /**
     * Generate a hash from an arbitrary value.
     *
     * @param mixed $value The value.
     *
     * @return string The hash.
     */
    public function hash($value);
}
