<?php

namespace FabianMichael\PluginStorage;

use Closure;
use Kirby\Cms\Page;

class Storage extends Page
{
    public function add(array $data = []): StorageItem
    {
        $uid = $data['uid'] ?? null;
        unset($data['uid']);
        
        $timestamp = $data['date'] ?? time();
        unset($data['date']);

        if ($uid === null) {
            do {
                // generate a unique ID from the microtime timestamp. To
                // mitigate the risk of creating multiple entries with the
                // same ID, we attempt this as often, as the generated key
                // does not already exist.
                $uid = str_replace('.', '', sprintf('%.4F', microtime(true)));
            } while ($this->index()->filterBy('uid', $uid)->first() !== null);
        }

        $root = "{$this->root()}/{$uid}";

        $data = array_merge($data, [
            'created' => $timestamp,
            'updated' => $timestamp,
        ]);        

        return $this->createChild([
            'root' => $root,
            'parent' => $this,
            'template' => 'storageitem',
            'dirname' => $uid,
            'uid' => $uid,
            'slug' => $uid,
            'content' => $data ?? [],
        ]);
    }

    protected function commit(string $action, array $arguments, Closure $callback)
    {
        $old = $this->hardcopy();

        $this->rules()->$action(...$arguments);
        $this->kirby()->trigger('storage.' . $action . ':before', ...$arguments);
        $result = $callback(...$arguments);
        $this->kirby()->trigger('storageItem.' . $action . ':after', $result, $old);
        $this->kirby()->cache('storage')->flush();
        return $result;
    }
}