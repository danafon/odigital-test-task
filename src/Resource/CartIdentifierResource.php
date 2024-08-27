<?php

namespace App\Resource;

use App\Entity\Cart;

class CartIdentifierResource
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
        ];
    }
}