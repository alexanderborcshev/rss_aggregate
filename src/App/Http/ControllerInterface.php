<?php

namespace App\Http;

interface ControllerInterface
{
    public function run(RequestInterface $request);
}