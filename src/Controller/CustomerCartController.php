<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api')]
class CustomerCartController extends AbstractController
{
    use ImplementsApi;

    #[Route('/customers/{customerId}/carts', name: 'customer_carts', methods: 'GET')]
    public function index(EntityManagerInterface $entityManager, $customerId): JsonResponse
    {
        $customerRepository = $entityManager->getRepository(Customer::class);
        /** @var Customer */
        $customer = $customerRepository->find($customerId);
        if (!$customer)
        {
            $data = [
                'status' => 404,
                'errors' => "Customer not found",
            ];

            return $this->response($data, 404);
        }
        $cart = $customer->getCart();
        
        return $this->response($cart);
    }

    #[Route('/customers/{customerId}/carts', name: 'carts_add', methods: 'POST')]
    public function save(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, $customerId): JsonResponse
    {
        try
        {
            $customerRepository = $entityManager->getRepository(Customer::class);
            /** @var Customer */
            $customer = $customerRepository->find($customerId);
            if (!$customer) {
                $data = [
                    'status' => 404,
                    'errors' => "Customer not found",
                ];
    
                return $this->response($data, 404);
            }
            $cart = $customer->getCart();
            if ($cart !== null) {
                $data = [
                    'status' => 422,
                    'errors' => "Customer already has a cart",
                ];
    
                return $this->response($data, 422);
            }

            $data = $this->transformJsonBody($request)?->get('data');
            if (!$data
                || !($data['type'] === 'carts'))
            {
                throw new \Exception("Data is not valid");
            }
            
            $cart = new Cart();
            $cart->setCustomer($customer);

            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->response($cart, 201);
        } 
        catch (\Exception $e)
        {
            throw $e;
        }
    }
 }