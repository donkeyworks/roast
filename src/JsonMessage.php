<?php

namespace DonkeyWorks\Roast;

/**
 * Json implementation of the DonkeyWorks\Roast\MessageInterface
 *
 * Adds JsonSerializable to the default Message implementation
 * to get a correct representation of the message after
 * passing it to json_encode
 *
 * @author sbouw
 */
class JsonMessage extends Message implements \JsonSerializable
{
    /**
     * Create and return an array that correctly represents the instance's state when passed to json_encode
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $export = [
            "message" => $this->getMessage()
        ];

        if ($this->getCode()) {
            $export["code"] = $this->getCode();
        }

        if ($this->getField()) {
            $export["field"] = $this->getField();
        }

        return $export;
    }
}
