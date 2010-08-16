<?php

namespace NetteExtras\ExtensionObjects\Tests;

require_once __DIR__ . '/Classes/Greeter.php';

/**
 * Test class for ExtensibleObject.
 */
class ExtensibleObjectTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Classes\Greeter
	 */
	protected $object;


	protected function setUp()
	{
		$this->object = new Classes\Greeter();
	}


	public function testCalls()
	{
		$this->assertEquals('Hello, World!', $this->object->hello());
		$this->assertEquals('Bonjour, World !', $this->object->bonjour());
	}

	public function testProperties()
	{
		$this->assertEquals(42, $this->object->property);
		$this->assertEquals(42, $this->object->helloProperty);
		$this->assertEquals(TRUE, $this->object->roProperty);
		$this->object->property = $this->object->helloProperty = 142;
		$this->assertEquals(142, $this->object->property);
		$this->assertEquals(142, $this->object->helloProperty);
	}

	/**
	 * @expectedException MemberAccessException
	 */
	public function testReadOnlyProperty()
	{
		$this->object->roProperty = FALSE;
		$this->fail();
	}
}
