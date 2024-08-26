<?php

 namespace App\Controller;

 use Doctrine\ORM\EntityManagerInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\Routing\Annotation\Route;

  #[Route('/api', name: 'customer_api')]
 abstract class ApiController extends AbstractController
 {
  abstract public function index(EntityManagerInterface $entityManager): JsonResponse;

  abstract public function save(Request $request, EntityManagerInterface $entityManager);

  abstract public function show(EntityManagerInterface $entityManager, $id);

  abstract public function update(Request $request, EntityManagerInterface $entityManager, $id);

  abstract public function delete(EntityManagerInterface $entityManager, $id);

  /**
   * Returns a JSON response
   *
   * @param array $data
   * @param $status
   * @param array $headers
   * @return JsonResponse
   */
  public function response($data, $status = 200, $headers = [])
  {
   return new JsonResponse(['data' => $data], $status, $headers);
  }

  protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
  {
   $data = json_decode($request->getContent(), true);

   if ($data === null) {
    return $request;
   }

   $request->request->replace($data);

   return $request;
  }

 }