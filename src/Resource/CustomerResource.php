<?php

namespace App\Resource;

use App\Entity\Customer;

class CustomerResource
{
    use FormatsDate;

    public Customer $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function toArray()
    {
        $customer = $this->customer;
        return [
            'id' => $customer->getId(),
            'type' => 'customers',
            'attributes' => [
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'created_at' => $this->formatDate($customer->getCreatedAt()),
                'updated_at' => $this->formatDate($customer->getUpdatedAt()),
            ]
        ];
    }
}