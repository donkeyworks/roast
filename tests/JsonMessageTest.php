<?php

namespace DonkeyWorks\Roast\Test;

use DonkeyWorks\Roast\JsonMessage;

/**
 * @group result
 *
 * @author sbouw
 */
class JsonMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test jsonSerialize
     *
     * @dataProvider jsonSerializeProvider
     *
     * @param string $message
     * @param int|null $code
     * @param string|null $field
     * @param array $expectedResult
     * @return void
     */
    public function testJsonSerialize($message, $code, $field, $expectedResult)
    {
        // Act
        $result = new JsonMessage($message, $code, $field);

        // Assert
        $this->assertEquals($expectedResult, $result->jsonSerialize());
    }

    // Data providers

    /**
     * Provide data for jsonSerialize tests
     *
     * @return array
     */
    public function jsonSerializeProvider()
    {
        return [
            ["Lorem ipsum", null, null, ["message" => "Lorem ipsum"]],
            ["Lorem ipsum", 1, null, ["message" => "Lorem ipsum", "code" => 1]],
            ["Lorem ipsum", null, "dolor", ["message" => "Lorem ipsum", "field" => "dolor"]],
            ["Lorem ipsum", 1, "dolor", ["message" => "Lorem ipsum", "code" => 1, "field" => "dolor"]]
        ];
    }
}
