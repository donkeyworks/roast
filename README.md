# roast

## Description

Donkeyworks Roast is a PHP library enabling consistent response formatting for AJAX requests based on an extended version of the JSend schema (https://github.com/rolfvreijdenberger/json-response-schema).

This repository contains a reference to json-schemas that extend the jsend schema so you can adhere to a standard for structured json responses for applications, focusing specifically on structuring response data in a consistent format as an application-level standard. As such, Roast allows for easy creation of a consistent result object of any type which can be serialized for transmission over HTTP. This means that Roast does not handle the transmission for you, other packages, like symfony's HttpFoundation are better equiped for that job. Roast just handles the structuring of the response data itself.

Currently Roast ships with one type of result object: JsonResult. But Roast can be easily extended to create your own type of result object if the JsonResult object does not suit your needs.

## Installation

```
composer require donkeyworks/roast
```

## Requirements

- PHP 5.6 or higher

## Usage

The following is a usage example of Roast formatting result data from an operation as Json, combined with symfony's HttpFoundation to send the formatted result to the client:

```php
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
    // If the doSomething method throwed an exception, add an error message to the JsonResult
    $result->setStatusError();
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

```javascript
{
    "status": "success",
    "data": "Some data, could be an object, an array or anything really, like this string"
}
```
The above response would be achieved with the following code:
```php
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
```javascript
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
```php
use DonkeyWorks\Roast\JsonResult;
use DonkeyWorks\Roast\Message;
use Symfony\Component\HttpFoundation\Response;

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

```javascript
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
```php
use DonkeyWorks\Roast\JsonResult;
use DonkeyWorks\Roast\Message;
use Symfony\Component\HttpFoundation\Response;

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

## Result object types

Altough most frequently used, the extended jsend specification is not limited to Json. The specification could also be used to format data returned by an XML-RPC webservice or for that matter any future standard that may arise. Who knows, you might want to return data as yaml. The specification allows you to structure your data consistantly across requests, but does not limit you in the type of response you want to send. Therefore Roast does not limit you either.

Roast's structure consists of "result objects" for specific response "types", like Json or XML. These "result objects" define a serialize method that serializes the data it holds to the proper "type". In the case of a Json "result object" this means calling PHP's native "json_encode" method to serialize the data. 

Since Json is the most used reponse format for webservices, Roast ships with a "result object type" that offers een implementation of serialization to Json. But it is trivial to add your own implementation for, say, XML.

See the basic usage example and the examples in the section "status code" on how to use the "result objects". These examples use the JsonResult object but apply to any type of result object, just instantiate the right type.

### Creating your own result object type

Creating a "result object type", e.g. for XML basically means you have to implement the `Donkeyworks\Roast\ResultInterface`. But to help you out a little bit and make a solid implementation easier we added an abstract class that you can extend and does most of the plumbing for you: `Donkeyworks\Roast\AbstractResult`.

By implementing the two abstract methods "serializeResult" and "serializeException" you can create your own "result object type" in a jiffy.

#### serializeResult

The serializeResult method receives an stdClass of the proper structure to adhere to the extended jsend specification. All you have to do is convert it to a string. See the JsonResult class for an example implementation. Off course something could go wrong while serializing, if that happens, throw an Exception, that will trigger the serializeException method.

#### serializeException

The serializeException method gets called when serialization throws an exception. This allows for the output to always be consistent and not suddenly return an "uncaught exception" message to the client in an unexpected format. The serializeException message receives the throwed exception so you can construct a descent error message for the consuming client. Make sure the serializeException method always returns a string of the proper format (that is, adhering to the jsend extend specification). See the JsonResult class for an example implementation.

### JsonResult

Roast ships with the JsonResult class as an implementation of the "json result object type".

By setting data or messages onto an instance of the JsonResult class one can add "data" to the result. After adding data and setting the proper status call the "serialize" method of the JsonResult instance to receive a valid json string which can be send to the client

Upon initialization or with the `setJsonOptions` method the json options for json_encode can be set. See http://php.net/manual/en/function.json-encode.php for more info on the available options.

## Messages

When something goes wrong with your operation, e.g. an exception was thrown or some validation failed, you can add messages to the result object to render a consistent response to the client.

Roast ships with a default message class that adheres to the "message" specification of jsend extend.

Messages will only be shown once the status of the result object is set to "fail" or "error".

Please see the examples under "result status codes" in this document.

## Custom data formatting

Sometimes you might want to give the result object an object as its data that does not "serialize" well. Think of an object with private properties and public getters. In these cases you can let the "data object" implement the `DonkeyWorks\Roast\ExportableInterface`. This interface defines one export method that allows you to transform your object to the proper format for serialization. 

If an object implements the ExportableInterface the export method will be called to obtain its data upon serialization.
