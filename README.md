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

The following is a usage example of Roast formatting a result data from an operation as Json, combined with symfony's HttpFoundation to send the formatted result to the client:

```
use DonkeyWorks\Roast\JsonResult;
use DonkeyWorks\Roast\Message;
use Symfony\Component\HttpFoundation\Response;

$result = new JsonResult();

// Do something that generates some data that should be send to the client
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

$response->send(); // Send the response to the client
```

## Result status codes

Jsend-extend defines three types of statuses: success, fail and error

### Success
The success state is, obviously, for successful responses. These responses will normally be sent with a 200 HTTP status code.

Roast's result objects default to the success state, but also define a `setStatusSuccess` method to manually set the status of the result object to "success".

The following is an example of a Roast result object with the status set to success, serialized as json: 

```
{
    "status": "success",
    "data": "Some data, could be an object, an array or anything really, like this string"
}
```
The above response would be achieved with the following code:
```
use DonkeyWorks\Roast\JsonResult;
use Symfony\Component\HttpFoundation\Response;

$result = new JsonResult();
$result->setData("Some data, could be an object, an array or anything really, like this string");

$response = new Response();
$response->headers->set('Content-Type', 'application/json');
$response->setContent($result->serialize());
$response->send();
```
### Fail
The "fail" state is for failed operations, generally because some check on an input parameter failed. These responses will mostly be sent with a 400 HTTP status code. E.g. if a request was made for a specific resource but an invalid resource id was passed, this could result in the following "fail" response:
```
{
    "status": "fail",
    "data": [
        {
            "message": "Unable to retrieve resource, invalid id passed.",
            "code": 1,
            "field": "resource-id"
        }
    ]
}
```
The above response would be achieved with the following code:
```
use DonkeyWorks\Roast\JsonResult;
use Symfony\Component\HttpFoundation\Response;
use DonkeyWorks\Roast\Message;

$result = new JsonResult();
$result->setStatusFail();
$result->addMessage(
    new Message("Unable to retrieve resource, invalid id passed.", 1, "resource-id")
);

$response = new Response();
$response->headers->set('Content-Type', 'application/json');
$response->setStatusCode(Response::HTTP_BAD_REQUEST);
$response->setContent($result->serialize());
$response->send();
```
The code and field properties of the message object are optional. The second and third parameter of the Message's constructor can therefore be left empty. Multiple messages can be added to the result object if e.g. multiple input checks failed.

### Error
The "error" state is for operations where an error occured while processing the request. E.g. an exception was thrown. These responses will normally be sent with a 500 HTTP status code. E.g. if an operation depends on an external webservice and that webservice is down this could result in the following "error" response:

```
{
    "status": "error",
    "data": [
        {
            "message": "An exception occured while processing your request. Timeout while trying to connect to external webservice."
        }
    ]
}
```
The above response would be achieved with the following code:
```
use DonkeyWorks\Roast\JsonResult;
use Symfony\Component\HttpFoundation\Response;
use DonkeyWorks\Roast\Message;

$result = new JsonResult();
try {
    throw new \Exception("Timeout while trying to connect to external webservice.");
} catch (\Exception $e) {
    $result->setStatusError();
    $result->addMessage(
        new Message("An exception occured while processing your request. " . $e->getMessage())
    );
}

$response = new Response();
$response->headers->set('Content-Type', 'application/json');
$response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
$response->setContent($result->serialize());
$response->send();
```

## JsonResult

## Creating your own result object type

## Messages