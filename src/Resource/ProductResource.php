<?php

namespace App\Resource;

use App\Entity\Product;

class ProductResource
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
            'attributes' => [
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'created_at' => $this->formatDate($product->getCreatedAt()),
                'updated_at' => $this->formatDate($product->getUpdatedAt()),
            ]
        ];
    }
}