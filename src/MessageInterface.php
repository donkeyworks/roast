<?php

namespace DonkeyWorks\Roast;

/**
 * Interface for a result message as defined in the JSend-extend schema
 *
 * See https://github.com/rolfvreijdenberger/jsend-json-schema/blob/master/jsend-extend-fail-error-json-schema.json for the schema definition.
 *
 * @author sbouw
 */
interface MessageInterface
{
    /**
     * Set the message for the message object
     *
     * @throws InvalidArgumentException
     * @param string $message
     * @return MessageInterface Return the instance to allow method chaining
     */
    public function setMessage($message);

    /**
     * Get the message string
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set the code for the message
     *
     * @throws InvalidArgumentException
     * @param int $code
     * @return MessageInterface Return the instance to allow method chaining
     */
    public function setCode($code);

    /**
     * Get the code
     *
     * @return int|float|string|null
     */
    public function getCode();

    /**
     * Set the field for the message
     *
     * @throws InvalidArgumentException
     * @param string $field
     * @return MessageInterface Return the instance to allow method chaining
     */
    public function setField($field);

    /**
     * Get the field
     *
     * @return string|null
     */
    public function getField();
}
