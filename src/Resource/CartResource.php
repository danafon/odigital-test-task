<?php

namespace App\Resource;

use App\Entity\Cart;

class CartResource
{
    use FormatsDate;

    public Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function toArray()
    {
        $cart = $this->cart;
        return [
            'id' => $cart->getId(),
            'type' => 'carts',
            'attributes' => [
                'created_at' => $this->formatDate($cart->getCreatedAt()),
                'updated_at' => $this->formatDate($cart->getUpdatedAt()),
            ],
            'relationships' => [
                'customer' => (new CustomerIdentifierResource($cart->getCustomer()))->toArray()
            ]
        ];
    }
}