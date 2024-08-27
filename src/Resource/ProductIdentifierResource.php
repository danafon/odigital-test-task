<?php

namespace App\Resource;

use App\Entity\Product;

class ProductIdentifierResource
{
    use FormatsDate;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function toArray()
    {
        $product = $this->product;
        return [
            'id' => $product->getId(),
            'type' => 'products',
        ];
    }
}