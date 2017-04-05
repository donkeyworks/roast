# roast

## Description

Donkeyworks Roast is a PHP library enabling consistent response formatting for AJAX requests based on an extended version of the JSend schema (https://github.com/rolfvreijdenberger/json-response-schema).

Roast allows for easy creation of a consistent result object of any type which can be serialized for transmission over HTTP. This means that Roast does not handle the transmission for you, other packages, like symfony's HttpFoundation are better equiped for that job. Roast just handles the formatting of the data itself.

Currently Roast ships with one type of result object: JsonResult. But Roast can be easily extended to create your own type of result object if the JsonResult object does not suit your needs.

## Installation

```
composer require donkeyworks/roast
```

## Requirements

- PHP 5.6 or higher

## Usage

The following is a usage example of Roast formatting a result data from an operation a Json combined with symfony's HttpFoundation to send the formatted result to the client:

```
use DonkeyWorks\Roast\JsonResult;
use DonkeyWorks\Roast\Message;
use Symfony\Component\HttpFoundation\Response;

$result = new JsonResult();

// Do something that generates a result
try {
    $data = $myService->doSomething();

    // Assign the data to the JsonResult
    $result->setData($data);
} catch (\Exception $e) {
    // If the doSomething method failed add an error message to the JsonResult
    $result->setStatusFail();
    $result->addMessage(
        new Message("Unable to 'doSomething'. " . $e->getMessage());
    );
}

// Use symfony's HttpFoundation to create the response
$response = new Response();
$response->headers->set('Content-Type', 'application/json');

// Serialize the JsonResult for consistently formatted response data adhering to jsend-extend specs wether
// it is regular data resulting from the operation or an error message
$response->setContent($result->serialize());
```

## Custom data formatting

## Error messages

## Creating your own result object type