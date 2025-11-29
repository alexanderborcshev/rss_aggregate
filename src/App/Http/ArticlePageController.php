<?php
namespace App\Http;

use App\Repository\NewsRepository;

class ArticlePageController extends BaseController implements ControllerInterface
{
    public function run(RequestInterface $request): void
    {
        $id = $request->getId();
        if ($id <= 0) {
            http_response_code(400);
            echo 'Bad request';
        }

        $newsRepo = new NewsRepository();
        $item = $newsRepo->getById($id);
        if (!$item) {
            http_response_code(404);
            echo 'Not found';
        }

        $this->render('article.php', [
            'item' => $item,
        ]);
    }
}
