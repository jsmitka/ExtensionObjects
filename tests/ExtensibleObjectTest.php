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


	public function __construct($name = NULL, array $data = array(), $dataName = '')
	{
		parent::__construct($name, $data, $dataName);

		Classes\Greeter::extensionObject('Dynamic');
	}

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
		$this->assertEquals(42, $this->object->ownProperty);
		$this->assertEquals(42, $this->object->helloProperty);
		$this->assertEquals(TRUE, $this->object->roProperty);
		$this->object->ownProperty = $this->object->helloProperty = 142;
		$this->assertEquals(142, $this->object->ownProperty);
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

	/**
	 * @expectedException MemberAccessException
	 */
	public function testProtectedProperty()
	{
		$var = $this->object->protectedProperty;
		$this->fail();
	}


	private $eventFired = FALSE;

	public function testEvents()
	{
		$this->object->onEvent[] = \callback($this, 'onEventHandler');
		$this->object->onEvent();
		$this->assertTrue($this->eventFired);
	}

	public function onEventHandler()
	{
		$this->eventFired = TRUE;
	}


	/**
	 * @expectedException MemberAccessException
	 */
	public function testDynamic()
	{
		$this->assertTrue($this->object->dynamicMethod());
		$plain = new Classes\Plain();
		$plain->dynamicMethod();
		$this->fail('Extension Object "Dynamic" has been also added to ExtensibleObject "Plain".');
	}
}
