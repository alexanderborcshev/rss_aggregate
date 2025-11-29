<?php
namespace App\View;

class Renderer
{
    private mixed $basePath;

    public function __construct($basePath = null)
    {
        // Путь к папке с шаблонами
        $this->basePath = $basePath ?: __DIR__ . '/templates';
    }

    /**
     * Рендерит PHP-шаблон, передавая в него переменные.
     * @param string $template Имя файла шаблона без пути (например, 'index.php')
     * @param array $vars Ассоциативный массив переменных
     * @return string HTML
     */
    public function render(string $template, array $vars = []): string
    {
        $file = rtrim($this->basePath, '/\\') . DIRECTORY_SEPARATOR . $template;
        if (!is_file($file)) {
            throw new \RuntimeException('Template not found: ' . $file);
        }
        // Извлекаем переменные в область видимости шаблона
        if (!empty($vars) && is_array($vars)) {
            extract($vars);
        }
        ob_start();
        include $file;
        return (string)ob_get_clean();
    }
}
