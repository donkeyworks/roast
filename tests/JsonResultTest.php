<?php

namespace DonkeyWorks\Roast\Test;

use DonkeyWorks\Roast\JsonResult;
use DonkeyWorks\Roast\Message;

/**
 * @group result
 *
 * @author sbouw
 */
class JsonResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setting valid json options and getting them afterwards
     *
     * @dataProvider setGetValidJsonOptionsProvider
     *
     * @param int $options
     * @return void
     */
    public function testSetGetValidJsonOptions($options)
    {
        // Arrange
        $result = new JsonResult();

        // Act
        $result->setJsonOptions($options);

        // Assert
        $this->assertEquals($options, $result->getJsonOptions());
    }

    /**
     * Test setting invalid json options
     *
     * @dataProvider setGetInvalidJsonOptionsProvider
     * @expectedException \InvalidArgumentException
     *
     * @param mixed $data
     * @return void
     */
    public function testSetInvalidJsonOptions($options)
    {
        // Arrange
        $result = new JsonResult();

        // Act
        $result->setJsonOptions($options);
    }

    /**
     * Test setting options in constructor
     *
     * @return void
     */
    public function testConstructor()
    {
        // Arrange
        $options = JSON_HEX_QUOT;

        // Act
        $result = new JsonResult($options);

        // Assert
        $this->assertEquals($options, $result->getJsonOptions());
    }

    /**
     * Test serialization of the result object
     *
     * @dataProvider serializeResultProvider
     *
     * @param string $status
     * @param Object|array $data
     * @param MessageInterface[] $messages
     * @param string $expectedSerializeResult
     * @return void
     */
    public function testSerializeResult($statusSetter, $data, $messages, $expectedSerializeResult)
    {
        // Arrange
        $result = new JsonResult();
        $result->$statusSetter();
        $result->setData($data);
        foreach ($messages as $message) {
            $result->addMessage($message);
        }

        // Act
        $serializeResult = $result->serialize();

        // Assert
        $this->assertEquals($expectedSerializeResult, $serializeResult);
    }

    /**
     * Test serialization of an exception thrown during serialization of the result object
     *
     * @dataProvider serializeExceptionProvider
     *
     * @param string $status
     * @param int|null $code
     * @param string $expectedSerializeResult
     * @return void
     */
    public function testSerializeException($message, $code, $expectedSerializeResult)
    {
        // Arrange
        $result = $this->getMockBuilder("\DonkeyWorks\Roast\JsonResult")
                        ->setMethods(["serializeResult"])
                        ->getMock();
        $result->expects($this->once())
                ->method("serializeResult")
                ->will($this->throwException(new \Exception($message, $code)));

        // Act
        $serializeResult = $result->serialize();

        // Assert
        $this->assertEquals($expectedSerializeResult, $serializeResult);
    }

    // Data providers

    /**
     * Provide valid data for setJsonOptions
     *
     * @return array
     */
    public function setGetValidJsonOptionsProvider()
    {
        return [
            [JSON_HEX_QUOT],
            [JSON_HEX_TAG | JSON_HEX_AMP]
        ];
    }

    /**
     * Provide invalid data for setJsonOptions
     *
     * @return array
     */
    public function setGetInvalidJsonOptionsProvider()
    {
        return [
            [null],
            [true],
            ["lorem ipsum"],
            [array("lorem ipsum")]
        ];
    }

    /**
     * Provide valid data for serializeResult
     *
     * @return array
     */
    public function serializeResultProvider()
    {
        return [
            [
                "setStatusSuccess",
                (object) ["lorem" => "ipsum"],
                [new Message("Dolor sit amet")],
                json_encode((object) ["status" => "success", "data" => (object)["lorem" => "ipsum"]])
            ],
            [
                "setStatusFail",
                (object) ["lorem" => "ipsum"],
                [new Message("Dolor sit amet")],
                json_encode((object) ["status" => "fail", "data" => [(object)["message" => "Dolor sit amet"]]])
            ],
            [
                "setStatusError",
                (object) ["lorem" => "ipsum"],
                [new Message("Dolor sit amet")],
                json_encode((object) ["status" => "error", "data" => [(object)["message" => "Dolor sit amet"]]])
            ],
            [
                "setStatusError",
                (object) ["lorem" => "ipsum"],
                [new Message("Dolor sit amet"), new Message("Adipiscing")],
                json_encode((object) ["status" => "error", "data" => [(object)["message" => "Dolor sit amet"], (object)["message" => "Adipiscing"]]])
            ],
        ];
    }

    /**
     * Provide valid data for serializeException
     *
     * @return array
     */
    public function serializeExceptionProvider()
    {
        return [
            [
                "Lorem ipsum",
                1,
                json_encode((object) ["status" => "error", "data" => [(object)["message" => "An exception occurred during serialization with the message: 'Lorem ipsum' (code: 1)."]]])
            ],
            [
                "Lorem ipsum",
                null,
                json_encode((object) ["status" => "error", "data" => [(object)["message" => "An exception occurred during serialization with the message: 'Lorem ipsum'."]]])
            ]
        ];
    }
}
