<?php

namespace App\Controller;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api')]
class CartController extends AbstractController
{
    use ImplementsApi;

    #[Route('/carts', name: 'carts', methods: 'GET')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $cartRepository = $entityManager->getRepository(Cart::class);
        $data = $cartRepository->findAll();
        
        return $this->response($data);
    }

    #[Route('/carts/{id}', name: 'carts_get', methods: 'GET')]
    public function show(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $cartRepository = $entityManager->getRepository(Cart::class);
        $cart = $cartRepository->find($id);

        if (!$cart)
        {
            $data = [
                'status' => 404,
                'errors' => "Cart not found",
            ];

            return $this->response($data, 404);
        }

        return $this->response($cart);
    }

    #[Route('/carts/{id}', name: 'carts_delete', methods: 'DELETE')]
    public function delete(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $cartRepository = $entityManager->getRepository(Cart::class);
        $cart = $cartRepository->find($id);

        if (!$cart){
            $data = [
                'status' => 404,
                'errors' => "Cart not found",
            ];

            return $this->response($data, 404);
        }

        $entityManager->remove($cart);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'success' => "Cart deleted successfully",
        ];

        return $this->response($data);
    }
 }