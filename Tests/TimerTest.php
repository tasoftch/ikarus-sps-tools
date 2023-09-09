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

use Ikarus\SPS\Tool\Timing\Timer;
use PHPUnit\Framework\TestCase;

class TimerTest extends TestCase
{
	public function testDefaultTimer() {
		$t = new Timer(100);
		$this->assertFalse($t->isTimeUp());

		usleep(50e3);
		$this->assertFalse($t->isTimeUp());

		usleep(60e3);
		$this->assertTrue($t->isTimeUp());
	}

	public function testDependentTimer() {
		$t = new Timer(100, Timer::TIMER_UNIT_MILLI_SECONDS, false);
		$this->assertTrue($t->isTimeUp());

		$t->reset();

		usleep(50e3);
		$this->assertFalse($t->isTimeUp());

		usleep(60e3);
		$this->assertTrue($t->isTimeUp());
	}

	public function testInvalidatedTimer() {
		$t = new Timer(100);
		$this->assertFalse($t->isTimeUp());

		$t->invalidate();
		usleep(60e3);
		$this->assertTrue($t->isTimeUp());
	}

	public function testTimeout() {
		$t = new Timer(100);
		usleep(60e3);

		$t = $t->getTimeout();
		$this->assertEquals(60, $t*1000, "", 3);

		$t = new Timer(100);
		$t->invalidate();

		$this->assertEquals(-1, $t->getTimeout(), "", 0.01);

	}
}
