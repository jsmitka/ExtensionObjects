<?php
/**
 * Copyright (c) 2009 Jan Smitka <jan@smitka.org>
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  * Neither the name of Pioneers of the Inevitable, Songbird, nor the names
 *    of its contributors may be used to endorse or promote products derived
 *    from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author		Jan Smitka <jan@smitka.org>
 * @copyright	Copyright (c) 2010 Jan Smitka <jan@smitka.org>
 * @license	New BSD licence
 */


namespace NetteExtras\ExtensionObjects\Tests\Classes;

use NetteExtras\ExtensionObjects\ExtensibleObject;
use NetteExtras\ExtensionObjects\ExtensionObject;


/**
 * @use(Hello, Bonjour, French)
 */
class Greeter extends ExtensibleObject
{
	protected $name = 'World';

	protected function getMark()
	{
		return '!';
	}


	private $property = 42;

	public function getProperty()
	{
		return $this->property;
	}

	public function setProperty($property)
	{
		$this->property = $property;
	}
}


class Hello extends ExtensionObject
{
	public function hello()
	{
		return 'Hello, ' . $this->name . $this->getMark();
	}


	private $helloProperty = 42;

	public function getHelloProperty()
	{
		return $this->helloProperty;
	}

	public function setHelloProperty($helloProperty)
	{
		$this->helloProperty = $helloProperty;
	}


}


class Bonjour extends ExtensionObject
{
	public function bonjour()
	{
		return 'Bonjour, ' . $this->name . $this->getFrenchMark();
	}

	private $roProperty = TRUE;

	public function getRoProperty()
	{
		return $this->roProperty;
	}
}


class French extends ExtensionObject
{
	protected function getFrenchMark()
	{
		return ' !';
	}
}
