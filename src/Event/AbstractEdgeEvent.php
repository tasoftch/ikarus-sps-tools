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

namespace Ikarus\SPS\Tool\Event;

use Ikarus\Raspberry\Pin\InputPinInterface;
use Ikarus\SPS\Register\MemoryRegisterInterface;

abstract class AbstractEdgeEvent implements CyclicEventTriggerInterface
{
	/** @var callable */
	private $trigger;

	/** @var int|null */
	private $old_state;

	private $timeout = 0, $last_timeout;

	private $debounce = 0;

	/**
	 * @param callable|InputPinInterface $trigger
	 */
	public function __construct($trigger, float $debounce = 0)
	{
		if($trigger instanceof InputPinInterface) {
			$trigger = function() use ($trigger) {
				return $trigger->getValue();
			};
		}
		$this->trigger = $trigger;
		$this->old_state = ($this->trigger)();
		$this->timeout = microtime(true);
		$this->debounce = $debounce;
	}

	public function hasEvent(): bool
	{
		$mt = microtime(true);

		$s = ($this->trigger)();
		if($s !== $this->old_state) {
			$cache = $s;

			if($mt - $this->timeout > $this->debounce) {
				if($this->isEvent($this->old_state, $s, $cache)) {
					$this->last_timeout = $mt - $this->timeout;
					$this->timeout = $mt;
					$this->old_state = $cache;
					return true;
				}
			}

			$this->old_state = $cache;
		}
		return false;
	}

	/**
	 * Returns the time interval since the last occurred event
	 *
	 * @return float
	 */
	public function getTimeout(): float {
		return $this->last_timeout;
	}

	/**
	 * Decides, if the state change is an event or not.
	 * This method must return the new state to wait for changes.
	 *
	 * @param $old_state
	 * @param $new_state
	 * @return bool
	 */
	abstract protected function isEvent($old_state, $new_state, &$cache_state = NULL): bool;
}