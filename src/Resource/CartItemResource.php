<?php

namespace App\Resource;

use App\Entity\CartItem;

class CartItemResource
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
            'type' => 'cartItems',
            'attributes' => [
                'quantity' => $cartItem->getQuantity(),
                'created_at' => $this->formatDate($cartItem->getCreatedAt()),
                'updated_at' => $this->formatDate($cartItem->getUpdatedAt()),
            ]
        ];
    }
}