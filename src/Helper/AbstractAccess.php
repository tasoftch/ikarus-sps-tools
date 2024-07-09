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

namespace Ikarus\SPS\Tool\Helper;

use Ikarus\SPS\Register\MemoryRegisterInterface;

abstract class AbstractAccess
{
	/** @var string */
	private $domain;
	/** @var string */
	private $key;

	/** @var MemoryRegisterInterface */
	protected $memoryRegister;

	/**
	 * @param string $domain
	 * @param string $key
	 */
	public function __construct(string $domain, string $key = NULL)
	{
		if(func_num_args() < 2) {
			list($this->domain, $this->key) = explode(".", $domain, 2);
		} else {
			$this->domain = $domain;
			$this->key = $key;
		}
	}

	/**
	 * @return mixed|string
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * @return mixed|string|null
	 */
	public function getKey()
	{
		return $this->key;
	}

	public function __invoke($value = NULL)
	{
		if(func_num_args() < 1) {
			return $this->readFromAccess($this->domain, $this->key);
		} else {
			$this->writeToAccess($value, $this->domain, $this->key);
		}
		return $value;
	}

	/**
	 * @param string $domain
	 * @param string $key
	 * @return mixed
	 */
	abstract protected function readFromAccess(string $domain, string $key);

	/**
	 * @param $value
	 * @param string $key
	 * @param string $domain
	 * @return void
	 */
	abstract protected function writeToAccess($value, string $key, string $domain);


	public function setMemoryRegister(MemoryRegisterInterface $memoryRegister): AbstractAccess
	{
		$this->memoryRegister = $memoryRegister;
		return $this;
	}
}