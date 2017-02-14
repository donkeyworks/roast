<?php

namespace DonkeyWorks\Roast;

/**
 * Interface for a Jsend-xtend result object
 *
 * See https://github.com/rolfvreijdenberger/jsend-json-schema/blob/master/jsend-extend-fail-error-json-schema.json for the schema definition.
 * Defines all required fields and a serialize method serializes the contents of the object to a string that follows the definition.
 *
 * @author sbouw
 */
interface ResultInterface
{
    /**
     * Set data for the result
     *
     * @throws InvalidArgumentException
     * @param string|null|array|object|int|float|bool $data
     * @return ResultInterface Return the instance to allow method chaining
     */
    public function setData($data);

    /**
     * Get the result data
     *
     * @return string|null|array|object|int|bool
     */
    public function getData();

    /**
     * Set status success for the result
     *
     * @return ResultInterface Return the instance to allow method chaining
     */
    public function setStatusSuccess();

    /**
     * Set status fail for the result
     *
     * @return ResultInterface Return the instance to allow method chaining
     */
    public function setStatusFail();

    /**
     * Set status error for the result
     *
     * @return ResultInterface Return the instance to allow method chaining
     */
    public function setStatusError();

    /**
     * Get the result status
     *
     * Defaults to the success status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Add a message to the result
     *
     * @param MessageInterface $message
     * @return ResultInterface Return the instance to allow method chaining
     */
    public function addMessage(MessageInterface $message);

    /**
     * Get the result messages
     *
     * @return MessageInterface[]
     */
    public function getMessages();

    /**
     * Return wether the result has either a fail or error status
     *
     * @return bool
     */
    public function hasTrouble();

    /**
     * Return wether the result has a success status
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Return wether the result has an error status
     *
     * @return bool
     */
    public function isError();

    /**
     * Return wether the result has a fail status
     *
     * @return bool
     */
    public function isFailure();

    /**
     * Serialize the result instance to a string so it can be transmitted over http
     *
     * @return string
     */
    public function serialize();
}
