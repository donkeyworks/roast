<?php

namespace DonkeyWorks\Roast;

/**
 * Base implementation of the Jsend-extend result object
 *
 * @see \DonkeyWorks\Roast\ResultInterface
 * @author sbouw
 */
abstract class AbstractResult implements ResultInterface
{
    /**
     * @var string Success status identifier
     */
    const RESULT_STATUS_SUCCESS = "success";

    /**
     * @var string Error status identifier
     */
    const RESULT_STATUS_ERROR = "error";

    /**
     * @var string Fail status identifier
     */
    const RESULT_STATUS_FAIL = "fail";

    /**
     * @var string|null|array|object|int|bool Result data
     */
    protected $data;

    /**
     * @var string Result status
     */
    protected $status = self::RESULT_STATUS_SUCCESS;

    /**
     * @var MessageInterface[] Array of result messages
     */
    protected $messages = [];

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::setData
     */
    public function setData($data)
    {
        if (
            !is_string($data)
            && !is_null($data)
            && !is_array($data)
            && (!is_object($data) || is_a($data, "Closure")) // Data can be any object, except "closure" which are anonymous functions and should not be allowed according to the specs
            && !is_numeric($data)
            && !is_bool($data)
        ) {
            throw new \InvalidArgumentException("Unable to set result data. Expected an argument of type an allowed type (string, null, array, object, int or bool).");
        }

        $this->data = $data;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::getData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::setStatusSuccess
     */
    public function setStatusSuccess()
    {
        $this->status = self::RESULT_STATUS_SUCCESS;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::setStatusFail
     */
    public function setStatusFail()
    {
        $this->status = self::RESULT_STATUS_FAIL;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::setStatusError
     */
    public function setStatusError()
    {
        $this->status = self::RESULT_STATUS_ERROR;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::getStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::addMessage
     */
    public function addMessage(MessageInterface $message)
    {
        if (!is_array($this->messages)) {
            $this->messages = [];
        }

        $this->messages[] = $message;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::getMessages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::hasTrouble
     */
    public function hasTrouble()
    {
        return in_array($this->status, [self::RESULT_STATUS_ERROR, self::RESULT_STATUS_FAIL]);
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::isSuccess
     */
    public function isSuccess()
    {
        return $this->status === self::RESULT_STATUS_SUCCESS;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::isError
     */
    public function isError()
    {
        return $this->status === self::RESULT_STATUS_ERROR;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::isFailure
     */
    public function isFailure()
    {
        return $this->status === self::RESULT_STATUS_FAIL;
    }

    /**
     * @see \DonkeyWorks\Roast\ResultInterface::serialize
     */
    public function serialize()
    {
        $result = '';

        try {
            $result = $this->serializeResult($this->export());
        } catch (\Exception $e) {
            // Something went wrong while serializing, serialize the exception and asign that to the result instead
            $result = $this->serializeException($e);
        }

        return $result;
    }

    /**
     * Get a representation of the result which is ready to be serialized
     *
     * Checks internal state for invariant exceptions
     * Replaces data with messages on trouble
     * Exports the data if the class implements the ExportableInterface
     *
     * @throws \Exception
     * @return \stdClass
     */
    protected function export()
    {
        $result = new \stdClass();

        if (is_null($this->status)) {
            throw new \Exception("Invalid empty status. Set a valid status on the result object before serializing.");
        }

        $result->status = $this->status;

        if ($this->hasTrouble()) {
            $result->data = [];
            foreach ($this->messages as $message) {
                $result->data[] = ($message instanceof ExportableInterface) ? $message->export() : $message;
            }
        } else {
            $result->data = ($this->data instanceof ExportableInterface) ? $this->data->export() : $this->data;
        }

        return $result;
    }

    /**
     * Primitive result serialization operation
     *
     * @throws \Exception Serialization exception
     * @param \stdClass $resultData Exported result data, ready for serialization
     * @return string Serialized result object
     */
    abstract protected function serializeResult(\stdClass $resultData);

    /**
     * Primitive exception serialization operation
     *
     * This method should never throw an exception, so "serialize"
     * by returning a static string, optionally interpolating the
     * exception message and code (don't forget escaping)
     *
     * @param \Exception $e
     * @return string Serialized exception
     */
    abstract protected function serializeException(\Exception $e);
}
