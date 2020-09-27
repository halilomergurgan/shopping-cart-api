<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Store
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response|\Slim\Http\Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $userId = (int)$args['userId'];
        $productId = (int)$request->getParam('product_id');
        $countOfProduct = (int)$request->getParam('count');

        if ($userId) {
            $productCount = \App\Models\Cart::
            selectRaw('id, sum(count) as existing_product_count')
                ->where('user_id', '=', $userId)
                ->where('product_id', '=', $productId)
                ->where('has_purchased', '=', 0)
                ->groupBy(['id'])
                ->first();

            switch ($productCount['existing_product_count']) {
                case null:
                    if ($countOfProduct <= 0) {
                        return ResponseHelper::error('Enter plus value!', 400);
                    }

                    \App\Models\Cart::create([
                        'product_price' => $request->getParam('product_price') * $countOfProduct,
                        'count' => $countOfProduct,
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'has_purchased' => false,
                        'category_id' => (int)$request->getParam('category_id')
                    ]);
                    break;
                case
                    $countOfProduct > 0:
                    $lastCount = $countOfProduct + $productCount['existing_product_count'];

                    \App\Models\Cart::where('id', $productCount['id'])
                        ->update([
                            'count' => $lastCount,
                            'product_price' => $request->getParam('product_price') * $lastCount,
                        ]);
                    break;
                case ($countOfProduct * -1) >= (int)$productCount['existing_product_count']:
                    $cart = \App\Models\Cart::find($productCount['id']);
                    $cart->delete();
                    break;
                case ($countOfProduct * -1) <= (int)$productCount['existing_product_count']:
                    $lastCount = $countOfProduct + (int)$productCount['existing_product_count'];

                    \App\Models\Cart::where('id', $productCount['id'])
                        ->update([
                            'count' => $lastCount,
                            'product_price' => $request->getParam('product_price') * $lastCount,
                        ]);
                    break;
            }

            return ResponseHelper::success('Your cart has been successfully updated!', 200);
        }

        return ResponseHelper::error('an Error occurred!', 400);
    }
}