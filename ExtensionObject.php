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
 * @license		New BSD licence
 */


namespace NetteExtras\ExtensionObjects;

use Nette\Reflection\ClassReflection;



/**
 * Extension object.
 *
 * @author		Jan Smitka <jan@smitka.org>
 * @copyright	Copyright (c) 2010 Jan Smitka <jan@smitka.org>
 * @package		NetteExtras\ExtensionObjects
 */
abstract class ExtensionObject
{
	/** @var ExtensibleObject */
	private $object;

	/** @var ClassReflection */
	private $reflection;

	/** @var ClassReflection */
	private $objectReflection;


	public function __construct(ExtensibleObject $object)
	{
		$this->object = $object;
		$this->objectReflection = new ClassReflection($object);
		$this->reflection = new ClassReflection($this);
	}


	/**
	 * Returns reflection for this extension object.
	 * @return ClassReflection
	 */
	public function getReflection()
	{
		return $this->reflection;
	}


	/**
	 * Provides access to ExtensibleObject's methods.
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		if ($this->objectReflection->hasMethod($name) && ($method = $this->objectReflection->getMethod($name)) && $method->isProtected()) {
			$method->setAccessible(TRUE);
			return $method->invokeArgs($this->object, $args);
		} else {
			return \callback($this->object, $name)->invokeArgs($args);
		}
	}


	/**
	 * Provides read access to ExtensibleObject's properties.
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if ($this->objectReflection->hasProperty($name) && ($property = $this->objectReflection->getProperty($name)) && $property->isProtected()) {
			$property->setAccessible(TRUE);
			return $property->getValue($this->object);
		} else {
			return $this->object->$name;
		}
	}


	/**
	 * Provides write access to ExtensibleObject's properties.
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		if ($this->objectReflection->hasProperty($name) && ($property = $this->objectReflection->getProperty($name)) && $property->isProtected()) {
			$property->setAccessible(TRUE);
			$property->setValue($this->object, $value);
		} else {
			$this->object->$name = $value;
		}
	}
}