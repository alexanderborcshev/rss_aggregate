<?php

namespace App\Http;

interface RequestInterface
{
    public function toArray(): array;
}