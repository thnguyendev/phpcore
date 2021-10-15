<?php
namespace Psr\Http\Message;

class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return (new Response())->withStatus($code, $reasonPhrase = '');
    }
}
?>