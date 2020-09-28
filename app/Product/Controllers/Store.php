<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Cart;

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

        if (!$userId) {
            return ResponseHelper::error('an Error occurred!', 400);
        }

        $productCount = Cart::selectRaw('id, sum(count) as existing_product_count')
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->where('has_purchased', '=', 0)
            ->groupBy(['id'])
            ->first();

        if (!$productCount['existing_product_count']) {
            if ($countOfProduct <= 0) {
                return ResponseHelper::error('Enter positive number!', 400);
            }

            $this->addToCart(
                $userId,
                $productId,
                $countOfProduct,
                (int)$request->getParam('category_id'),
                $request->getParam('product_price')
            );

            return ResponseHelper::success('Your product has been successfully added!', 200);
        }

        if ($countOfProduct > 0) {
            $lastCount = $countOfProduct + $productCount['existing_product_count'];
            $this->updateCartCount($productCount['id'], $lastCount, $request->getParam('product_price'));
        } else {
            if (($countOfProduct * -1) >= (int)$productCount['existing_product_count']) {
                $this->deleteFromCart($productCount['id']);
            } else {
                $lastCount = $countOfProduct + (int)$productCount['existing_product_count'];
                $this->updateCartCount($productCount['id'], $lastCount, $request->getParam('product_price'));
            }
        }

        return ResponseHelper::success('Your cart has been successfully updated!', 200);
    }

    /**
     * @param int $userId
     * @param int $productId
     * @param int $count
     * @param int $categoryId
     * @param float $productPrice
     * @return Cart
     */
    private function addToCart(int $userId, int $productId, int $count, int $categoryId, float $productPrice): Cart
    {
        return Cart::create([
            'product_price' => $productPrice * $count,
            'count' => $count,
            'user_id' => $userId,
            'product_id' => $productId,
            'has_purchased' => false,
            'category_id' => $categoryId
        ]);
    }

    /**
     * @param int $cartId
     * @param int $lastCount
     * @param float $productPrice
     * @return bool
     */
    private function updateCartCount(int $cartId, int $lastCount, float $productPrice): bool
    {
        return Cart::where('id', $cartId)
            ->update([
                'count' => $lastCount,
                'product_price' => $productPrice * $lastCount,
            ]);
    }

    /**
     * @param int $cartId
     * @return bool
     */
    private function deleteFromCart(int $cartId): bool
    {
        $cart = Cart::find($cartId);

        return $cart->delete();
    }
}