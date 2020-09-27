<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Cart
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return \Slim\Http\Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $userCarts = \App\Models\Cart::with('category')
            ->where('user_id', '=', (int)$args['userId'])
            ->where('has_purchased', '=', 0)
            ->get();

        if ($userCarts->isNotEmpty()) {
            return ResponseHelper::compact(null, 200, $userCarts);
        }

        return ResponseHelper::error('an Error occurred!', 200);
    }
}