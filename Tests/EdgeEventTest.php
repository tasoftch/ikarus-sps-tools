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

use Ikarus\SPS\Tool\Event\AllEdgesEvent;
use Ikarus\SPS\Tool\Event\FallingEdgeEvent;
use Ikarus\SPS\Tool\Event\RisingEdgeEvent;
use PHPUnit\Framework\TestCase;

class EdgeEventTest extends TestCase
{
	public function testAllEdgesEvent() {
		$trigger = 1;
		$edge = new AllEdgesEvent(function() use (&$trigger) {
			return $trigger;
		});

		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 2;
		$this->assertTrue($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 4;
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 2;
		$this->assertTrue($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 1;
		$this->assertTrue($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());
	}

	public function testRisingEdge() {
		$trigger = 1;
		$edge = new RisingEdgeEvent(function() use (&$trigger) {
			return $trigger;
		});

		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 2;
		$this->assertTrue($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 1;

		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 4;
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 2;
		$this->assertTrue($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 2;
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());
	}

	public function testFallingEdge() {
		$trigger = 1;
		$edge = new FallingEdgeEvent(function() use (&$trigger) {
			return $trigger;
		});

		$trigger = 2;
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 1;
		$this->assertTrue($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 4;
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());

		$trigger = 2;
		$this->assertFalse($edge->hasEvent());
		$this->assertFalse($edge->hasEvent());
	}

	public function testTimeout() {
		$trigger = 1;
		$edge = new AllEdgesEvent(function() use (&$trigger) {
			return $trigger;
		});

		$trigger = 2;
		usleep(10e3);
		$this->assertTrue($edge->hasEvent());
		$s = $edge->getTimeout();
		$this->assertEquals(0.01, $s, "", 0.005);
	}

	public function testDebouncingEdge() {
		$trigger = 1;
		$edge = new AllEdgesEvent(function() use (&$trigger) {
			return $trigger;
		}, 0.1);

		$trigger = 2;
		usleep(10e3);
		$this->assertFalse($edge->hasEvent());

		$trigger = 1;
		usleep(10e3);
		$this->assertFalse($edge->hasEvent());

		$trigger = 2;
		usleep(10e3);
		$this->assertFalse($edge->hasEvent());

		$trigger = 1;
		usleep(100e3);
		$this->assertTrue($edge->hasEvent());
	}
}
