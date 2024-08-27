<?php

 namespace App\Controller;

 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;

trait ImplementsApi
 {
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse(['data' => $data], $status, $headers);
    }

    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

 }