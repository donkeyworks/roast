<?php

namespace DonkeyWorks\Roast\Test;

use DonkeyWorks\Roast\Message;

/**
 * @group result
 *
 * @author sbouw
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test valid use of message constructor
     *
     * Also covers all setters and getters defined in the MessageInterface
     *
     * @dataProvider constructorProvider
     *
     * @param string $message
     * @param int|null $code
     * @param string|null $field
     * @return void
     */
    public function testConstructor($message, $code, $field)
    {
        // Act
        $result = new Message($message, $code, $field);

        // Assert
        $this->assertEquals($message, $result->getMessage());
        $this->assertEquals($code, $result->getCode());
        $this->assertEquals($field, $result->getField());
    }

    /**
     * Test invalid use of message constructor
     *
     * @dataProvider constructorExceptionProvider
     * @expectedException \Exception
     *
     * @param string $message
     * @param int|null $code
     * @param string|null $field
     * @return void
     */
    public function testConstructorException($message, $code, $field)
    {
        // Act
        $result = new Message($message, $code, $field);
    }

    // Data providers

    /**
     * Provide data for constructor tests
     *
     * @return array
     */
    public function constructorProvider()
    {
        return [
            ["Lorem ipsum", null, null],
            ["Lorem ipsum", 1, null],
            ["Lorem ipsum", 1.1, null],
            ["Lorem ipsum", "dolor", null],
            ["Lorem ipsum", null, "dolor"],
            ["Lorem ipsum", 1, "dolor"]
        ];
    }

    /**
     * Provide data for constructor exception tests
     *
     * @return array
     */
    public function constructorExceptionProvider()
    {
        return [
            [null, null, null],
            [1, null, null],
            ["Lorem ipsum", [], null],
            ["Lorem ipsum", 1, []]
        ];
    }
}
