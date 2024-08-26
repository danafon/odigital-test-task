<?php

namespace App\Entity;

class CustomerResource{
    public static function toArray(Customer $customer)
    {
        return [
            'id' => $customer->getId(),
            'type' => 'customers',
            'attributes' => [
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'created_at' => $customer->getCreatedAt(),
                'updated_at' => $customer->getUpdatedAt(),
            ]
        ];
    }
}