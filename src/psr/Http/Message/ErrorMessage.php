<?php
namespace Psr\Http\Message;

class ErrorMessage
{
    /* Message */
    const invalidHeaderName = 'Header name must be a string complies with RFC 7230';
    const invalidHeaderValue = 'Header value must be an array of strings comply with RFC 7230';
    const invalidBody = 'Body must be a StreamInterface';
    /* Stream */
    const invalidHandle = 'Handle must be a resource';
    const invalidPointer = 'Unable to get current position of pointer';
    const unseekable = 'Stream is unseekable';
    const failedSeeking = 'Unable to seek to position %s with whence %s';
    const unwritable = 'Stream is unwritable';
    const failedWriting = 'Unable to write to stream';
    const unreadable = 'Stream is unreadable';
    const failedReading = 'Unable to read from stream';
    /* Uri */
    const invalidScheme = 'Invalid scheme';
    const unsupportedScheme = 'Unsupported scheme';
    const invalidHostname = 'Invalid hostname';
    const invalidPort = 'Invalid port';
    const invalidPath = 'Invalid path';
    const invalidQuery = 'Invalid query string';
    const invalidFragment = 'Invalid fragment';
    /* Response */
    const invalidStatusCode = 'Invalid status code';
    /* Request */
    const invalideRequestTarget = 'Invalid request target';
    const unsupportedMethod = 'Unsupported HTTP method';
    /* ServerRequest */
    const invalidParsedBody = 'Invalid parsed body';
    /* UploadedFile */
    const noStreamAvailable = 'No stream is available';
    const noStreamCreated = 'No stream can be created';
    const uploadedFileMoved = 'Uploaded file already moved';
    const invalidTargetPath = 'Invalid target path';
    const errorMoving = 'Error moving uploaded file';
}
?>