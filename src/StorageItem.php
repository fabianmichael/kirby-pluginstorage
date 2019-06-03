<?php

namespace FabianMichael\PluginStorage;

use Closure;
use Kirby\Cms\Page;

class StorageItem extends Page
{
    protected function commit(string $action, array $arguments, Closure $callback)
    {
        $old = $this->hardcopy();

        $this->rules()->$action(...$arguments);
        $this->kirby()->trigger('storageItem.' . $action . ':before', ...$arguments);
        $result = $callback(...$arguments);
        $this->kirby()->trigger('storageItem.' . $action . ':after', $result, $old);
        $this->kirby()->cache('pages')->flush();
        return $result;
    }

    public function update(array $input = null, string $language = null, bool $validate = false)
    {
        $input = array_merge([
            'updated' => time(),
        ], $input);
        
        return parent::update($input, $language, $validate);
    }
}