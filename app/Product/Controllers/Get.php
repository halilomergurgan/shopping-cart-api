<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Get
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $client = new GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $response = $client->get('https://halil.free.beeceptor.com/products'
        );

        return $response;
    }
}