<?php

namespace DonkeyWorks\Roast;

/**
 * Basic implementation of the message defined in the JSend-extend result object
 *
 * @see \DonkeyWorks\Roast\MessageInterface
 * @author sbouw
 */
class Message implements MessageInterface
{
    /**
     * @var string Message string
     */
    protected $message;

    /**
     * @var int|string Message code
     */
    protected $code;

    /**
     * @var string Field on which the message applies
     */
    protected $field;

    public function __construct($message, $code = null, $field = null)
    {
        // Use setters to make sure we pass argument validation
        $this->setMessage($message);
        $this->setCode($code);
        $this->setField($field);
    }

    /**
     * @see \DonkeyWorks\Roast\MessageInterface::setMessage
     */
    public function setMessage($message)
    {
        if (!is_string($message)) {
            throw new \InvalidArgumentException("Unable to set message. Expected a string but received a " . gettype($message) . ".");
        }

        $this->message = $message;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\MessageInterface::getMessage
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @see \DonkeyWorks\Roast\MessageInterface::setCode
     */
    public function setCode($code)
    {
        if (
            !is_numeric($code)
            && !is_string($code)
            && !is_null($code)
        ) {
            throw new \InvalidArgumentException("Unable to set code. Expected an int or string but received a " . gettype($code) . ".");
        }

        $this->code = $code;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\MessageInterface::getCode
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @see \DonkeyWorks\Roast\MessageInterface::setField
     */
    public function setField($field)
    {
        if (!is_string($field) && !is_null($field)) {
            throw new \InvalidArgumentException("Unable to set field. Expected a string but received a " . gettype($field) . ".");
        }

        $this->field = $field;

        return $this;
    }

    /**
     * @see \DonkeyWorks\Roast\MessageInterface::getField
     */
    public function getField()
    {
        return $this->field;
    }
}
