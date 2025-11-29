<?php
namespace App\Repository;

use App\Bootstrap;
use App\Cache;
use App\Util;
use PDO;

class CategoryRepository
{
    private ?PDO $db;
    private Cache $cache;

    public function __construct()
    {
        $this->db = Bootstrap::db();
        $this->cache = new Cache();
    }

    public function getAll(): array
    {
        $key = 'categories_all';
        $cached = $this->cache->get($key);
        if (is_array($cached)) {
            return $cached;
        }

        $stmt = $this->db->query('SELECT id, name, slug FROM categories ORDER BY name');
        $rows = $stmt->fetchAll();
        $this->cache->set($key, $rows, 600);
        return $rows;
    }

    public function getOrCreateByName($name): array
    {
        $slug = Util::slugify($name);
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, slug)
             VALUES (:name, :slug)
             ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)'
        );
        $stmt->execute([':name' => $name, ':slug' => $slug]);
        $id = (int)$this->db->lastInsertId();

        return ['id' => $id, 'name' => $name, 'slug' => $slug];
    }

    public function getBySlug($slug)
    {
        $stmt = $this->db->prepare('SELECT id, name, slug FROM categories WHERE slug = :slug');
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }
}
