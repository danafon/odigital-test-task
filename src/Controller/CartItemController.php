<?php

namespace App\Controller;

use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api')]
class CartItemController extends AbstractController
{
    use ImplementsApi;

    #[Route('/cart-items/{id}', name: 'cart_items_get', methods: 'GET')]
    public function show(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $cartItemRepository = $entityManager->getRepository(CartItem::class);
        $cartItem = $cartItemRepository->find($id);

        if (!$cartItem)
        {
            $data = [
                'status' => 404,
                'errors' => "Cart Item not found",
            ];

            return $this->response($data, 404);
        }

        return $this->response($cartItem);
    }

    #[Route('/cart-items/{id}', name: 'cart_items_patch', methods: 'PATCH')]
    public function update(Request $request, EntityManagerInterface $entityManager, $id, ValidatorInterface $validator): JsonResponse
    {
        try{
            $cartItemRepository = $entityManager->getRepository(CartItem::class);
            /** @var CartItem */
            $cartItem = $cartItemRepository->find($id);

            if (!$cartItem)
            {
                $data = [
                    'status' => 404,
                    'errors' => "Cart Item not found",
                ];

                return $this->response($data, 404);
            }

            $data = $this->transformJsonBody($request)?->get('data');

            if (!$data
                || !($data['type'] === 'cart_items')
                || !$data['attributes']
                || !$data['attributes']['quantity'])
            {
                throw new \Exception("Data is not valid");
            }

            $cartItem->setQuantity($data['attributes']['quantity']);

            $errors = $validator->validate($cartItem);
            if (count($errors) > 0) {
                throw new Exception((string) $errors);
            }

            $entityManager->flush();

            return $this->response($cartItem, 201);
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => $e->getMessage(),
            ];

            return $this->response($data, 422);
        }   

    }

    #[Route('/cart-items/{id}', name: 'cart_items_delete', methods: 'DELETE')]
    public function delete(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $cartItemRepository = $entityManager->getRepository(CartItem::class);
        $cartItem = $cartItemRepository->find($id);

        if (!$cartItem){
            $data = [
                'status' => 404,
                'errors' => "Cart Item not found",
            ];

            return $this->response($data, 404);
        }

        $entityManager->remove($cartItem);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'success' => "Cart Item deleted successfully",
        ];

        return $this->response($data);
    }
 }