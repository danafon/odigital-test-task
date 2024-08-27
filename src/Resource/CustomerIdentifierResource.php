<?php

namespace App\Resource;

use App\Entity\Customer;

class CustomerIdentifierResource
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
            'type' => 'customers'
        ];
    }
}