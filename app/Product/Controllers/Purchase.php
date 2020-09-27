<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Purchase
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $userId = $args['userId'];

        if ($userId) {
            $products = \App\Models\Cart::
            select('user_id', 'product_id')
                ->where('user_id', '=', $userId)
                ->where('has_purchased', '=', 0)
                ->get();

            $outOfStockProducts = [];

            foreach ($products as $product) {
                $client = new GuzzleHttp\Client([
                    'headers' => ['Content-Type' => 'application/json']
                ]);

                $response = $client->post('http://halil.free.beeceptor.com/product/cart/reduction',
                    ['body' => json_encode(['stock_id' => $product['product_id']])]
                );

                $response = json_decode($response->getBody(), true);

                if ($response['status'] != true) {
                    $outOfStockProducts[] = $product['product_id'];
                }
            }

            if (count($outOfStockProducts) > 0) {
                return ResponseHelper::error('Some of your products are out of stock!', 400);
            }

            \App\Models\Cart::where('user_id', '=', $userId)
                ->update([
                    'has_purchased' => 1,
                ]);

            return ResponseHelper::success('Your purchase has been successfully!', 200);
        }
    }
}