<?php

namespace App\Http;

use App\View\Renderer;

class BaseController
{
    private Renderer $renderer;
    public function __construct()
    {
        $this->renderer = new Renderer();
    }

    protected function render($view, $params = []): void
    {
        echo $this->renderer->render($view, $params);
    }
}