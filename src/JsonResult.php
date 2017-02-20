<?php

namespace DonkeyWorks\Roast;

/**
 * Json implementation of the JSend-extend result object
 *
 * @see \DonkeyWorks\Roast\AbstractResult
 * @author sbouw
 */
class JsonResult extends AbstractResult
{
    /**
     * @var int Json_encode options param, see http://php.net/manual/en/function.json-encode.php
     */
    private $jsonOptions;

    /**
     * @see AbstractResult::createMessage
     */
    public static function createMessage($message, $code = null, $field = null)
    {
        return new JsonMessage($message, $code, $field);
    }

    /**
     * @param int $jsonOptions Json_encode options param, see http://php.net/manual/en/function.json-encode.php
     */
    public function __construct($jsonOptions = 0)
    {
        // Use setters to make sure we pass argument validation
        $this->setJsonOptions($jsonOptions);
    }

    /**
     * Set json options
     *
     * Json_encode options param, see http://php.net/manual/en/function.json-encode.php
     *
     * @param int $jsonOptions
     * @return JsonResult
     */
    public function setJsonOptions($jsonOptions)
    {
        if (!is_int($jsonOptions)) {
            throw new \InvalidArgumentException("Invalid jsonOptions value type " . gettype($jsonOptions) . ". See http://php.net/manual/en/json.constants.php for a list of valid values.");
        }

        $this->jsonOptions = $jsonOptions;

        return $this;
    }

    /**
     * Get the json options
     *
     * @return int|null
     */
    public function getJsonOptions()
    {
        return $this->jsonOptions;
    }

    /**
     * @see \DonkeyWorks\Roast\AbstractResult::serializeResult
     */
    protected function serializeResult(\stdClass $resultData)
    {
        $serializedResult = json_encode($resultData, $this->jsonOptions);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("An error occurred while serializing JsonResult: " . json_last_error_msg(), json_last_error());
        }

        return $serializedResult;
    }

    /**
     * @see \DonkeyWorks\Roast\AbstractResult::serializeException
     */
    protected function serializeException(\Exception $e)
    {
        $message = "An exception occurred during serialization with the message: '" . $e->getMessage() . "'" . ($e->getCode() ? " (code: " . $e->getCode() . ")" : null) . ".";

        $serializedException = json_encode((object) [
            "status" => self::RESULT_STATUS_ERROR,
            "data" => [
                (object) [
                    "message"=> $message
                ]
            ]
        ]);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Return a valid json string with error information at all times, even if the exception itself could not be serialized
            return '{"status": "' . self::RESULT_STATUS_ERROR . '", "data": [{"message": "An unknown exception occurred during serialization."}]}';
        }

        return $serializedException;
    }
}
