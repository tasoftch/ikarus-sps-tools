<?php
/*
 * BSD 3-Clause License
 *
 * Copyright (c) 2019, TASoft Applications
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 *  Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Ikarus\SPS\Tool\Condition;

use Ikarus\SPS\Tool\Timer;

abstract class AbstractCondition implements ConditionInterface
{
	/** @var Timer */
	private $timer;
	private $status = self::STATUS_INACTIVE;
	private $timeout = 0;

	/**
	 * @param Timer $timer
	 */
	public function __construct(Timer $timer)
	{
		$this->timer = $timer;
	}

	/**
	 * @param Timer $timer
	 * @return static
	 */
	public function setTimer(Timer $timer)
	{
		$this->timer = $timer;
		return $this;
	}

	public function getStatus(): int
	{
		if($this->status == self::STATUS_WAITING) {
			if($this->checkCondition()) {
				$this->timeout = $this->timer->getTimeout();
				$this->timer->invalidate();
				$this->status = self::STATUS_FULFILLED;
			} elseif ($this->timer->isTimeUp()) {
				$this->timeout = $this->timer->getTimeout();
				$this->timer->invalidate();
				$this->status = self::STATUS_TIMEOUT;
			}
		}
		return $this->status;
	}

	public function getTimeout(): float
	{
		if($this->status == self::STATUS_WAITING)
			return $this->timer->getTimeout();
		if($this->status == self::STATUS_FULFILLED)
			return $this->timeout;
		return -1;
	}

	public function startCondition()
	{
		$this->timer->reset();
		$this->status = self::STATUS_WAITING;
		$this->resetCondition();
	}

	/**
	 * @return void
	 */
	public function stopCondition() {
		$this->timer->invalidate();
		$this->status = self::STATUS_INACTIVE;
	}

	/**
	 * Resets the condition if needed
	 *
	 * @return void
	 */
	abstract protected function resetCondition();

	/**
	 * Checks, if the condition is fulfilled.
	 *
	 * @return bool
	 */
	abstract protected function checkCondition(): bool;
}