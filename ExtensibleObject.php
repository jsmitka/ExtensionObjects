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
 * @author Jan Smitka <jan@smitka.org>
 * @copyright Copyright (c) 2010 Jan Smitka <jan@smitka.org>
 * @license New BSD licence
 */


namespace NetteExtras\ExtensionObjects;

use Nette\Object;
use Nette\ObjectMixin;
use ReflectionMethod;



/**
 * An object, which could be extended by an ExtensionObject.
 *
 * @author		Jan Smitka <jan@smitka.org>
 * @copyright	Copyright (c) 2010 Jan Smitka <jan@smitka.org>
 * @package		NetteExtras\ExtensionObjects
 */
abstract class ExtensibleObject extends Object
{
	const USE_ANNOTATION = 'use';

	static private $classExtensions = array();

	/** @var array */
	private $extensions;

	public function __construct()
	{
		$this->initializeExtensions();
	}


	/**
	 * Attaches a new ExtensionObject to this ExtensibleObject descendant.
	 * @param string $className
	 */
	static public function extensionObject($className)
	{
		$class = \get_called_class();
		if (!isset(self::$classExtensions[$class])) {
			self::$classExtensions[$class] = array();
		}
		if (!in_array($className, self::$classExtensions[$class])) {
			\array_unshift(self::$classExtensions[$class], $className);
		}
	}


	public function __call($name, $args)
	{
		foreach ($this->getExtensions() as $extension) {
			/* @var $extension ExtensionObject */
			if ($extension->getReflection()->hasMethod($name)) {
				$method = $extension->getReflection()->getMethod($name);
				if ($method->isPublic() && !$method->isStatic() && !$method->isAbstract()) {
					return \callback($extension, $name)->invokeArgs($args);
				}
			}

			if ($extension->getReflection()->hasEventProperty($name)) {
				return ObjectMixin::call($extension, $name, $args);
			}
		}

		return parent::__call($name, $args);
	}


	public function &__get($name)
	{
		foreach ($this->getExtensions() as $extension) {
			/* @var $extension ExtensionObject */
			if ($extension->getReflection()->hasProperty($name)) {
				$property = $extension->getReflection()->getProperty($name);
				if ($property->isPublic() && !$property->isStatic()) {
					return $extension->$name;
				}
			}

			if (ObjectMixin::has($extension, $name)) {
				return ObjectMixin::get($extension, $name);
			}
		}

		return parent::__get($name);
	}

	public function __set($name, $value)
	{
		foreach ($this->getExtensions() as $extension) {
			/* @var $extension ExtensionObject */
			if ($extension->getReflection()->hasProperty($name)) {
				$property = $extension->getReflection()->getProperty($name);
				if ($property->isPublic() && !$property->isStatic()) {
					$extension->$name = $value;
					return;
				}
			}

			if (ObjectMixin::has($extension, $name)) {
				ObjectMixin::set($extension, $name, $value);
				return;
			}
		}

		return parent::__set($name, $value);
	}


	private function initializeExtensions()
	{
		$class = \get_class($this);
		$this->extensions = array();
		$reflection = $this->getReflection();
		if (isset(self::$classExtensions[$class]) || $reflection->hasAnnotation(self::USE_ANNOTATION)) {
			$baseNamespace = (($t = \strrpos($class = get_class($this), '\\')) ? \substr($class, 0, $t + 1) : '');
			$extensions = (array) $reflection->getAnnotation(self::USE_ANNOTATION);
			if (isset(self::$classExtensions[$class])) {
				$extensions = \array_merge(self::$classExtensions[$class], $extensions);
			}
			foreach ($extensions as &$extension) {
				if ($extension[0] != '\\' && $baseNamespace) {
					$extension = $baseNamespace . $extension;
				}
				$extensionObject = new $extension($this);
				if (!($extensionObject instanceof ExtensionObject)) {
					throw new TraitException('The extension object has to be derived from NetteExtras\ExtensionObjects\ExtensionObject.');
				}
				$this->extensions[$extension] = $extensionObject;
			}
		}
	}

	private function getExtensions()
	{
		if ($this->extensions === NULL) {
			$this->initializeExtensions();
		}
		return $this->extensions;
	}
}