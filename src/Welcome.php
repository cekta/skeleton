<?php

declare(strict_types=1);

namespace App;

use Cekta\Framework\HTTP\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Welcome implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return Response::json(['message' => 'welcome to cekta']);
    }
}