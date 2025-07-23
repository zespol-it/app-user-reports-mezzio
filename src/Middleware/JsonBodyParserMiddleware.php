<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');
        
        if (strpos($contentType, 'application/json') !== false) {
            $body = $request->getBody()->getContents();
            $parsedBody = json_decode($body, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($parsedBody);
            }
        }
        
        return $handler->handle($request);
    }
} 