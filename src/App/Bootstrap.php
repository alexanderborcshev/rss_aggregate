<?php
namespace App;

use Memcached;
use PDO;
use RuntimeException;

class Bootstrap
{
    private static ?array $config;
    private static ?PDO $pdo = null;
    private static ?Memcached $memcached = null;

    public static function config()
    {
        if (!isset(self::$config)) {
            $pathMain = dirname(__DIR__) . '/../config.php';
            if (file_exists($pathMain)) {
                self::$config = require $pathMain;
            } else {
                throw new RuntimeException('Config file not found. Create config.php '.$pathMain);
            }
            if (!empty(self::$config['app']['timezone'])) {
                date_default_timezone_set(self::$config['app']['timezone']);
            }
        }
        return self::$config;
    }

    public static function db(): ?PDO
    {
        if (self::$pdo === null) {
            $cfg = self::config()['db'];
            $charset = $cfg['charset'] ?? 'utf8mb4';
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['dbname'], $charset);
            self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$pdo;
    }

    public static function cache(): ?Memcached
    {
        if (self::$memcached === null) {
            $cfg = self::config()['memcached'];
            $m = new Memcached();
            $m->addServer($cfg['host'], (int)$cfg['port']);
            self::$memcached = $m;
        }
        return self::$memcached;
    }
}
