<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call\Renderer;

use Eloquent\Phony\Call\CallInterface;

/**
 * The interface implemented by call renderers.
 */
interface CallRendererInterface
{
    /**
     * Render the supplied call.
     *
     * @param CallInterface $call The call.
     *
     * @return string The rendered call.
     */
    public function render(CallInterface $call);
}