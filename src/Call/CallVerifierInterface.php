<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call;

use Eloquent\Phony\Assertion\Result\AssertionResultInterface;
use Exception;

/**
 * The interface implemented by call verifiers.
 */
interface CallVerifierInterface extends CallInterface
{
    /**
     * Get the call duration.
     *
     * @return float|null The call duration in seconds, or null if the call has not yet completed.
     */
    public function duration();

    /**
     * Get the call response duration.
     *
     * @return float|null The call response duration in seconds, or null if the call has not yet responded.
     */
    public function responseDuration();

    /**
     * Get the number of arguments.
     *
     * @return integer The number of arguments.
     */
    public function argumentCount();

    /**
     * Returns true if called with the supplied arguments (and possibly others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return boolean True if called with the supplied arguments.
     */
    public function calledWith();

    /**
     * Throws an exception unless called with the supplied arguments (and
     * possibly others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertCalledWith();

    /**
     * Returns true if called with the supplied arguments (and no others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return boolean True if called with the supplied arguments.
     */
    public function calledWithExactly();

    /**
     * Throws an exception unless called with the supplied arguments (and no
     * others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertCalledWithExactly();

    /**
     * Returns true if not called with the supplied arguments (and possibly
     * others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return boolean True if not called with the supplied arguments.
     */
    public function notCalledWith();

    /**
     * Throws an exception unless not called with the supplied arguments (and
     * possibly others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertNotCalledWith();

    /**
     * Returns true if not called with the supplied arguments (and no others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return boolean True if not called with the supplied arguments.
     */
    public function notCalledWithExactly();

    /**
     * Throws an exception unless not called with the supplied arguments (and no
     * others).
     *
     * @param mixed $argument,... The arguments.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertNotCalledWithExactly();

    /**
     * Returns true if this call occurred before the supplied call.
     *
     * @param CallInterface $call Another call.
     *
     * @return boolean True if this call occurred before the supplied call.
     */
    public function calledBefore(CallInterface $call);

    /**
     * Throws an exception unless this call occurred before the supplied call.
     *
     * @param CallInterface $call Another call.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertCalledBefore(CallInterface $call);

    /**
     * Returns true if this call occurred after the supplied call.
     *
     * @param CallInterface $call Another call.
     *
     * @return boolean True if this call occurred after the supplied call.
     */
    public function calledAfter(CallInterface $call);

    /**
     * Throws an exception unless this call occurred after the supplied call.
     *
     * @param CallInterface $call Another call.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertCalledAfter(CallInterface $call);

    /**
     * Returns true if the $this value is equal to the supplied value.
     *
     * @param object|null $value The possible $this value.
     *
     * @return boolean True if the $this value is equal to the supplied value.
     */
    public function calledOn($value);

    /**
     * Throws an exception unless the $this value is equal to the supplied
     * value.
     *
     * @param object|null $value The possible $this value.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertCalledOn($value);

    /**
     * Returns true if this call returned the supplied value.
     *
     * @param mixed $value The value.
     *
     * @return boolean True if this call returned the supplied value.
     */
    public function returned($value = null);

    /**
     * Throws an exception unless this call returned the supplied value.
     *
     * @param mixed $value The value.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertReturned($value = null);

    /**
     * Returns true if an exception of the supplied type was thrown.
     *
     * @param Exception|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return boolean True if a matching exception was thrown.
     */
    public function threw($type = null);

    /**
     * Throws an exception unless this call threw an exception of the supplied
     * type.
     *
     * @param Exception|string|null $type An exception to match, the type of exception, or null for any exception.
     *
     * @return AssertionResultInterface If the assertion passes.
     * @throws Exception                If the assertion fails.
     */
    public function assertThrew($type = null);
}
