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

namespace Ikarus\SPS\Tool\Timing;

class Sleeper implements TimingInterface
{
	private $timeout = 0;
	private $timer = -1;

	/**
	 * @param int $timeout
	 * @param int $timer
	 */
	public function __construct(int $timeout = 0, int $unit = self::TIMING_UNIT_MILLI_SECONDS)
	{
		switch ($unit) {
			case static::TIMING_UNIT_SECONDS:
				$timeout *= 1e6;
				break;
			case static::TIMING_UNIT_MILLI_SECONDS:
				$timeout *= 1e3;
				break;
			case static::TIMING_UNIT_MINUTES:
				$timeout *= 60 * 1e6;
				break;
		}
		$this->timeout = $timeout;
	}

	public function reset()
	{
		$this->timer = microtime(true);
	}

	public function sleep(bool $useTicks = true) {
		if($this->timer == -1)
			throw new \RuntimeException("You must reset a sleeper before use it.");

		if($this->timeout - (microtime(true) - $this->timer) * 1e6 < 0)
			goto finish;

		if($useTicks) {
			declare(ticks=1) {
				usleep($this->timeout - (microtime(true) - $this->timer) * 1e6);
			}
		} else {
			usleep($this->timeout - (microtime(true) - $this->timer) * 1e6);
		}

		finish:
		$this->timer = -1;
	}
}