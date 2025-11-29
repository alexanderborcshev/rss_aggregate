<?php

use App\Util;

/** @var array $item */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Util::h($item['title']) ?> — Новости</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#f6f7f9;color:#111}
        header{background:#1a73e8;color:#fff;padding:16px 20px}
        main{max-width:900px;margin:0 auto;padding:20px}
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:16px}
        .meta{color:#6b7280;font-size:12px;margin-bottom:12px}
        .title{margin:0 0 8px}
        .img{margin:8px 0}
        .img img{max-width:100%;height:auto;border-radius:6px}
        a{color:#1a73e8}
    </style>
</head>
<body>
<header>
    <h1><a href="/" style="color:#fff;text-decoration:none">← Ко всем новостям</a></h1>
</header>
<main>
    <article class="card">
        <h2 class="title"><?= Util::h($item['title']) ?></h2>
        <div class="meta">Опубликовано: <?= Util::h(date('d.m.Y H:i', strtotime($item['pub_date']))) ?></div>
        <?php if (!empty($item['image_url'])): ?>
            <div class="img"><img src="<?= Util::h($item['image_url']) ?>" alt=""></div>
        <?php endif; ?>
        <div class="content">
            <?php if (!empty($item['content'])): ?>
                <?= $item['content'] ?>
            <?php else: ?>
                <p><?= nl2br(Util::h(strip_tags($item['description']))) ?></p>
            <?php endif; ?>
        </div>
        <p style="margin-top:12px"><a href="<?= Util::h($item['link']) ?>" target="_blank">Читать оригинал на сайте</a></p>
    </article>
</main>
</body>
</html>
