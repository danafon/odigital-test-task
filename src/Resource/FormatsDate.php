<?php

namespace App\Resource;

trait FormatsDate
{
    private function formatDate(?\DateTimeImmutable $date)
    {
        return $date?->format('Y-m-d H:i:s');
    }
}