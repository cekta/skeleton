<?php

declare(strict_types=1);

namespace App;

use Cekta\Framework\HTTP\Response;
use Cekta\Framework\HTTP\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[Route\GET('/')]
class Welcome implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return Response::json(['message' => 'welcome to cekta']);
    }
}
