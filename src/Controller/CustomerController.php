<?php

 namespace App\Controller;

 use App\Entity\Customer;
 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\Routing\Annotation\Route;

 /**
  * Class CustomerController
  * @package App\Controller
  */
  #[Route('/api', name: 'customer_api')]
 class CustomerController extends ApiController
 {
    #[Route('/customers', name: 'customers', methods: 'GET')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $customerRepository = $entityManager->getRepository(Customer::class);
        $data = $customerRepository->findAll();
        
        return $this->response($data);
    }

    #[Route('/customers', name: 'customers_add', methods: 'POST')]
    public function save(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try
        {
            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name') || !$request->get('email')){
                throw new \Exception();
            }

            $customer = new Customer();
            $customer->setName($request->get('name'));
            $customer->setEmail($request->get('email'));
            $entityManager->persist($customer);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Customer added successfully",
            ];

            return $this->response($data);
        } 
        catch (\Exception $e) 
        {
            $data = [
                'status' => 422,
                'errors' => "Data is not valid",
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
    public function update(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
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

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('name') || !$request->request->get('email'))
            {
                throw new \Exception();
            }

            $customer->setName($request->get('name'));
            $customer->setEmail($request->get('email'));
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Customer updated successfully",
            ];

            return $this->response($data);
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
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
            'errors' => "Customer deleted successfully",
        ];

        return $this->response($data);
    }
 }