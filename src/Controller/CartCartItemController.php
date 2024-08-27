<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api')]
class CartCartItemController extends AbstractController
{
    use ImplementsApi;

    #[Route('/carts/{cartId}/cart-items', name: 'cart_cart_items', methods: 'GET')]
    public function index(EntityManagerInterface $entityManager, $cartId): JsonResponse
    {
        $cartRepository = $entityManager->getRepository(Cart::class);
        /** @var Cart */
        $cart = $cartRepository->find($cartId);
        if (!$cart)
        {
            $data = [
                'status' => 404,
                'errors' => "Cart not found",
            ];

            return $this->response($data, 404);
        }
        $cartItems = $cart->getCartItems()->toArray();
        
        return $this->response($cartItems);
    }

    #[Route('/carts/{cartId}/cart-items', name: 'cart_cart_items_add', methods: 'POST')]
    public function save(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $cartId): JsonResponse
    {
        try
        {
            $cartRepository = $entityManager->getRepository(Cart::class);
            /** @var Cart */
            $cart = $cartRepository->find($cartId);
            if (!$cart) {
                $data = [
                    'status' => 404,
                    'errors' => "Cart not found",
                ];
    
                return $this->response($data, 404);
            }

            $data = $this->transformJsonBody($request)?->get('data');

            if (!$data
                || !($data['type'] === 'cart_items')
                || !$data['attributes']
                || !$data['attributes']['quantity']
                || !$data['relationships']
                || !$data['relationships']['product']['data']['id'])
            {
                throw new \Exception("Data is not valid");
            }

            $productId = $data['relationships']['product']['data']['id'];
            $productRepository = $entityManager->getRepository(Product::class);
            /** @var Product */
            $product = $productRepository->find($productId);
            if (!$cart) {
                $data = [
                    'status' => 404,
                    'errors' => "Product not found",
                ];
    
                return $this->response($data, 404);
            }
            if (
                $cart->getCartItems()
                    ->filter(fn(CartItem $cartItem) => $cartItem->getProduct()->getId() === $productId)
                    ->count() > 0) 
            {
                $data = [
                    'status' => 422,
                    'errors' => "Cart already has such product",
                ];
    
                return $this->response($data, 422);
            }
            
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($data['attributes']['quantity']);

            $entityManager->persist($cartItem);
            $entityManager->flush();

            return $this->response($cartItem, 201);
        } 
        catch (\Exception $e)
        {
            throw $e;
        }
    }
 }