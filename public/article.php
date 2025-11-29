<?php
require_once __DIR__ . '/../src/autoload.php';

use App\Http\ArticlePageController;
use App\Http\ArticleRequest;

$controller = new ArticlePageController();
$request = new ArticleRequest((int) $_GET['id']);
$controller->run($request);
