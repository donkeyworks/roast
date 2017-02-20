<?php

namespace DonkeyWorks\Roast;

use \DonkeyWorks\Roast\AbstractResult;
use \DonkeyWorks\Roast\Message;

/**
 * @group result
 *
 * @author sbouw
 */
class AbstractResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setting valid data and getting it afterwards
     *
     * @dataProvider setGetValidDataProvider
     *
     * @param Object|array $data
     * @return void
     */
    public function testSetGetValidData($data)
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        $result->setData($data);

        // Assert
        $this->assertSame($data, $result->getData());
    }

    /**
     * Test setting invalid data
     *
     * @dataProvider setGetInvalidDataProvider
     * @expectedException \InvalidArgumentException
     *
     * @param Object|array $data
     * @return void
     */
    public function testSetInvalidData($data)
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        $result->setData($data);
    }

    /**
     * Test getting the default status
     *
     * @return void
     */
    public function testGetDefaultStatus()
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Assert
        $this->assertSame(AbstractResult::RESULT_STATUS_SUCCESS, $result->getStatus());
    }

    /**
     * Test setting success status and getting it afterwards
     *
     * @return void
     */
    public function testSetGetStatusSuccess()
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        $result->setStatusSuccess();

        // Assert
        $this->assertSame(AbstractResult::RESULT_STATUS_SUCCESS, $result->getStatus());
    }

    /**
     * Test setting fail status and getting it afterwards
     *
     * @return void
     */
    public function testSetGetStatusFail()
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        $result->setStatusFail();

        // Assert
        $this->assertSame(AbstractResult::RESULT_STATUS_FAIL, $result->getStatus());
    }

    /**
     * Test setting error status and getting it afterwards
     *
     * @return void
     */
    public function testSetGetStatusError()
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        $result->setStatusError();

        // Assert
        $this->assertSame(AbstractResult::RESULT_STATUS_ERROR, $result->getStatus());
    }

    /**
     * Test adding valid messages and getting them afterwards
     *
     * @dataProvider addGetValidMessagesProvider
     *
     * @param MessageInterface[] $messages
     * @return void
     */
    public function testAddGetValidMessages(array $messages)
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        foreach ($messages as $message) {
            $result->addMessage($message);
        }

        // Assert
        $this->assertSame($messages, $result->getMessages());
    }

    /**
     * Test evaluating wether a result has trouble or not
     *
     * @dataProvider hasTroubleProvider
     *
     * @param string|null $statusSetter If null the status will not be set
     * @param bool $trouble
     * @return void
     */
    public function testHasTrouble($statusSetter, $trouble)
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        if (!is_null($statusSetter)) {
            $result->$statusSetter();
        }

        // Assert
        $this->assertSame($trouble, $result->hasTrouble());
    }

    /**
     * Test evaluating wether a result has the success status or not
     *
     * @dataProvider isSuccessProvider
     *
     * @param string|null $statusSetter If null the status will not be set
     * @param bool $success
     * @return void
     */
    public function testIsSuccess($statusSetter, $success)
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        if (!is_null($statusSetter)) {
            $result->$statusSetter();
        }

        // Assert
        $this->assertSame($success, $result->isSuccess());
    }

    /**
     * Test evaluating wether a result has the error status or not
     *
     * @dataProvider isErrorProvider
     *
     * @param string|null $statusSetter If null the status will not be set
     * @param bool $error
     * @return void
     */
    public function testIsError($statusSetter, $error)
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        if (!is_null($statusSetter)) {
            $result->$statusSetter();
        }

        // Assert
        $this->assertSame($error, $result->isError());
    }

    /**
     * Test evaluating wether a result has the fail status or not
     *
     * @dataProvider isFailureProvider
     *
     * @param string|null $statusSetter If null the status will not be set
     * @param bool $error
     * @return void
     */
    public function testIsFailure($statusSetter, $error)
    {
        // Arrange
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");

        // Act
        if (!is_null($statusSetter)) {
            $result->$statusSetter();
        }

        // Assert
        $this->assertSame($error, $result->isFailure());
    }

    /**
     * Test serialization template method
     *
     * @return void
     */
    public function testSerializeSuccess()
    {
        // Arrange
        $dummySerializeResult = "Lorem ipsum";
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");
        $result->expects($this->once())
            ->method("serializeResult")
            ->willReturn($dummySerializeResult);

        // Act
        $serialized = $result->serialize();

        // Assert
        $this->assertSame($dummySerializeResult, $serialized);
    }

    /**
     * Test serialization template method exception handling
     *
     * @return void
     */
    public function testSerializeException()
    {
        // Arrange
        $exception = new \Exception;
        $dummySerializeResult = "Lorem ipsum";
        $result = $this->getMockForAbstractClass("\DonkeyWorks\Roast\AbstractResult");
        $result->expects($this->once())
            ->method("serializeResult")
            ->will($this->throwException($exception));
        $result->expects($this->once())
            ->method("serializeException")
            ->with($exception)
            ->willReturn($dummySerializeResult);

        // Act
        $serialized = $result->serialize();

        // Assert
        $this->assertSame($dummySerializeResult, $serialized);
    }

    // Data providers

    /**
     * Provide valid data for setData
     *
     * @return array
     */
    public function setGetValidDataProvider()
    {
        return [
            ["lorem ipsum"],
            [null],
            [["lorem" => "ipsum"]],
            [(object) ["lorem" => "ipsum"]],
            [new \DateTime()],
            [123456789],
            [1.23456789],
            [true]
        ];
    }

    /**
     * Provide invalid data for setData
     *
     * @return array
     */
    public function setGetInvalidDataProvider()
    {
        return [
            [function() {}],
            [curl_init()] // resource
        ];
    }

    /**
     * Provide valid data for addMessage
     *
     * @return array
     */
    public function addGetValidMessagesProvider()
    {
        // Should be able to set one or multiple messages
        return [
            [[], null],
            [[new Message("Lorem ipsum")]],
            [[new Message("Lorem ipsum"), new Message("Lorem ipsum")]],
        ];
    }

    /**
     * Provide test scenarios for hasTrouble
     *
     * @return array
     */
    public function hasTroubleProvider()
    {
        return [
            [null, false],
            ["setStatusSuccess", false],
            ["setStatusFail", true],
            ["setStatusError", true]
        ];
    }

    /**
     * Provide test scenarios for isSuccess
     *
     * @return array
     */
    public function isSuccessProvider()
    {
        return [
            [null, true],
            ["setStatusSuccess", true],
            ["setStatusFail", false],
            ["setStatusError", false]
        ];
    }

    /**
     * Provide test scenarios for isError
     *
     * @return array
     */
    public function isErrorProvider()
    {
        return [
            [null, false],
            ["setStatusSuccess", false],
            ["setStatusFail", false],
            ["setStatusError", true]
        ];
    }

    /**
     * Provide test scenarios for isFailure
     *
     * @return array
     */
    public function isFailureProvider()
    {
        return [
            [null, false],
            ["setStatusSuccess", false],
            ["setStatusFail", true],
            ["setStatusError", false]
        ];
    }
}
