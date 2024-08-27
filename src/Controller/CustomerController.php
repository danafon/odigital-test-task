<?php

namespace App\Controller;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'customer_api')]
class CustomerController extends AbstractController
{
    use ImplementsApi;

    #[Route('/customers', name: 'customers', methods: 'GET')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $customerRepository = $entityManager->getRepository(Customer::class);
        $data = $customerRepository->findAll();
        
        return $this->response($data);
    }

    #[Route('/customers', name: 'customers_add', methods: 'POST')]
    public function save(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse|Response
    {
        try
        {
            $data = $this->transformJsonBody($request)?->get('data');

            if (!$data
                || !($data['type'] === 'customers')
                || !$data['attributes']
                || !$data['attributes']['name']
                || !$data['attributes']['email'])
            {
                throw new \Exception("Data is not valid");
            }
            $customer = new Customer();
            $customer->setName($data['attributes']['name']);
            $customer->setEmail($data['attributes']['email']);

            $errors = $validator->validate($customer);

            if (count($errors) > 0) {
                throw new Exception((string) $errors);
            }

            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->response($customer, 201);
        } 
        catch (\Exception $e)
        {
            $data = [
                'status' => 422,
                'errors' => $e->getMessage(),
            ];

            return $this->response($data, 422);
        }
    }

    #[Route('/customers/{id}', name: 'customers_get', methods: 'GET')]
    public function show(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $customerRepository = $entityManager->getRepository(Customer::class);
        $customer = $customerRepository->find($id);

        if (!$customer)
        {
            $data = [
                'status' => 404,
                'errors' => "Customer not found",
            ];

            return $this->response($data, 404);
        }

        return $this->response($customer);
    }

    #[Route('/customers/{id}', name: 'customers_patch', methods: 'PATCH')]
    public function update(Request $request, EntityManagerInterface $entityManager, $id, ValidatorInterface $validator): JsonResponse
    {
        try{
            $customerRepository = $entityManager->getRepository(Customer::class);
            /** @var Customer */
            $customer = $customerRepository->find($id);

            if (!$customer)
            {
                $data = [
                    'status' => 404,
                    'errors' => "Customer not found",
                ];

                return $this->response($data, 404);
            }

            $data = $this->transformJsonBody($request)?->get('data');

            if (!$data
                || !($data['type'] === 'customers')
                || !$data['attributes']
                || !$data['attributes']['name']
                || !$data['attributes']['email'])
            {
                throw new \Exception("Data is not valid");
            }

            $customer->setName($data['attributes']['name']);
            $customer->setEmail($data['attributes']['email']);

            $errors = $validator->validate($customer);
            if (count($errors) > 0) {
                throw new Exception((string) $errors);
            }

            $entityManager->flush();

            return $this->response($customer, 201);
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => $e->getMessage(),
            ];

            return $this->response($data, 422);
        }   

    }

    #[Route('/customers/{id}', name: 'customers_delete', methods: 'DELETE')]
    public function delete(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $customerRepository = $entityManager->getRepository(Customer::class);
        $customer = $customerRepository->find($id);

        if (!$customer){
            $data = [
                'status' => 404,
                'errors' => "Customer not found",
            ];

            return $this->response($data, 404);
        }

        $entityManager->remove($customer);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'success' => "Customer deleted successfully",
        ];

        return $this->response($data);
    }
 }