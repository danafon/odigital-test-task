<?php

namespace App\Resource;

use App\Entity\CartItem;

class CartItemIdentifierResource
{
    use FormatsDate;

    public CartItem $cartItem;

    public function __construct(CartItem $cartItem)
    {
        $this->cartItem = $cartItem;
    }

    public function toArray()
    {
        $cartItem = $this->cartItem;
        return [
            'id' => $cartItem->getId(),
            'type' => 'cart_items',
        ];
    }
}