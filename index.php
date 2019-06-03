<?php

use FabianMichael\PluginStorage\Storage;
use FabianMichael\PluginStorage\StorageItem;
use Kirby\Cms\Site;

@include_once __DIR__ . '/vendor/autoload.php';

$methods = [
    'storage' => function(string $key): Storage {
        $key = "_{$key}";
        $root = "{$this->root()}/{$key}";

        return Storage::factory([
            'slug' => $key,
            'dirname' => $key,
            'template' => 'storage',
            'model' => 'storage',
            'root' => $root,
            'parent' => is_a($this, Site::class) ? null : $this,
        ]);
    },
];

Kirby::plugin('fabianmichael/pluginstorage', [
    'pageMethods' => $methods,
    'siteMethods' => $methods,
    'pageModels' => [
        'storage' => Storage::class,
        'storageitem' => StorageItem::class,
    ],
    'blueprints' => [
        'pages/storage' => __DIR__ . '/blueprints/storage.yml',
        'pages/storageitem' => __DIR__ . '/blueprints/storageitem.yml',
    ],
]);
