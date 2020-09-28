<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Cart;

class Order
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return \Slim\Http\Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $userOrders = Cart::with('category')
            ->where('user_id', '=', (int)$args['userId'])
            ->where('has_purchased', '=', 1)
            ->get();

        if ($userOrders->isNotEmpty()) {
            return ResponseHelper::compact(200, $userOrders);
        }

        return ResponseHelper::error('an Error occurred!', 200);
    }
}