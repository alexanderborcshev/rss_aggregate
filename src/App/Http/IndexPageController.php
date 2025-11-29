<?php
namespace App\Http;

use App\Bootstrap;
use App\Cache;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;

class IndexPageController extends BaseController implements ControllerInterface
{
    public function run(RequestInterface $request): void
    {
        $cfg = Bootstrap::config();
        $perPage = (int)$cfg['app']['items_per_page'];

        $categoriesRepository = new CategoryRepository();
        $newsRepository = new NewsRepository();
        $cache = new Cache();

        $catSlug = $request->getCategory();
        $catId = null;
        if ($catSlug !== '') {
            $catRow = $categoriesRepository->getBySlug($catSlug);
            if ($catRow && isset($catRow['id'])) {
                $catId = (int)$catRow['id'];
            }
        }

        $filters = [
            'category_id' => $catId,
            'date_from' => $request->getDateFrom(),
            'date_to' => $request->getDateTo(),
        ];

        $cacheKey = 'list_' . md5(json_encode([$filters, $request->getPage(), $perPage]));

        $result = $cacheKey ? $cache->get($cacheKey) : null;
        if (!is_array($result)) {
            $list = $newsRepository->findByFilters($filters, $request->getPage(), $perPage);
            $categories = $categoriesRepository->getAll();
            $result = [
                'list' => is_array($list) ? $list : [
                    'total' => 0,
                    'items' => [],
                    'page' => $request->getPage(),
                    'per_page' => $perPage,
                    'pages' => 1,
                ],
                'categories' => is_array($categories) ? $categories : [],
            ];
            if ($cacheKey) {
                $cache->set($cacheKey, $result, 120);
            }
        }

        $this->render('index.php', [
            'categories' => $result['categories'],
            'catSlug' => $request->getCategory(),
            'dateFrom' => $request->getDateFrom(),
            'dateTo' => $request->getDateTo(),
            'result' => $result['list'],
            'urlWith' => function (array $overrides = []) use ($request): string {
                $query = array_filter($request->toArray(), static function ($v) {
                    return $v !== '' && $v !== null;
                });
                $query = array_merge($query, $overrides);
                return '?' . http_build_query($query);
            },
        ]);
    }

}
