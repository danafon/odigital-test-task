<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'product_api')]
class ProductController extends AbstractController
{
    use ImplementsApi;

    #[Route('/products', name: 'products', methods: 'GET')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $data = $productRepository->findAll();
        
        return $this->response($data);
    }

    #[Route('/products', name: 'products_add', methods: 'POST')]
    public function save(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        try
        {
            $data = $this->transformJsonBody($request)?->get('data');

            if (!$data
                || !($data['type'] === 'products')
                || !$data['attributes']
                || !$data['attributes']['title']
                || !$data['attributes']['price']
                || !$data['attributes']['description'])
            {
                throw new \Exception("Data is not valid");
            }
            $product = new Product();
            $product->setTitle($data['attributes']['title']);
            $product->setDescription($data['attributes']['description']);
            $product->setPrice($data['attributes']['price']);

            $errors = $validator->validate($product);

            if (count($errors) > 0) {
                throw new Exception((string) $errors);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->response($product, 201);
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

    #[Route('/products/{id}', name: 'products_get', methods: 'GET')]
    public function show(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $product = $productRepository->find($id);

        if (!$product)
        {
            $data = [
                'status' => 404,
                'errors' => "Product not found",
            ];

            return $this->response($data, 404);
        }

        return $this->response($product);
    }

    #[Route('/products/{id}', name: 'products_patch', methods: 'PATCH')]
    public function update(Request $request, EntityManagerInterface $entityManager, $id, ValidatorInterface $validator): JsonResponse
    {
        try{
            $productRepository = $entityManager->getRepository(Product::class);
            /** @var Product */
            $product = $productRepository->find($id);

            if (!$product)
            {
                $data = [
                    'status' => 404,
                    'errors' => "Product not found",
                ];

                return $this->response($data, 404);
            }

            $data = $this->transformJsonBody($request)?->get('data');

            if (!$data
                || !($data['type'] === 'products')
                || !$data['attributes']
                || !$data['attributes']['title']
                || !$data['attributes']['description']
                || !$data['attributes']['price'])
            {
                throw new \Exception("Data is not valid");
            }

            $product->setTitle($data['attributes']['title']);
            $product->setDescription($data['attributes']['description']);
            $product->setPrice($data['attributes']['price']);

            $errors = $validator->validate($product);
            if (count($errors) > 0) {
                throw new Exception((string) $errors);
            }

            $entityManager->flush();

            return $this->response($product, 201);
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => $e->getMessage(),
            ];

            return $this->response($data, 422);
        }   

    }

    #[Route('/products/{id}', name: 'products_delete', methods: 'DELETE')]
    public function delete(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $product = $productRepository->find($id);

        if (!$product){
            $data = [
                'status' => 404,
                'errors' => "Product not found",
            ];

            return $this->response($data, 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'success' => "Product deleted successfully",
        ];

        return $this->response($data);
    }
 }