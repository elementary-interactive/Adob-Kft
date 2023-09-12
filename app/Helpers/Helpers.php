<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

if (!function_exists('block_template')) {
    function block_template($block): array
    {
        $template = 'block';

        $attrs = $block->getAttributes();

        $result = [];

        if (array_key_exists('template', $attrs) && !empty($attrs['template'])) {
            $result[] = Arr::first(app('site')->current()->domains) . ".partials.{$block->name()}.{$attrs['template']}";
            $result[] = "partials.{$block->name()}.{$attrs['template']}";
        }

        $result[] = Arr::first(app('site')->current()->domains) . ".partials.{$block->name()}.block";
        $result[] = "partials.{$block->name()}.block";

        // dump($result);

        return $result;
    }
}

if (!function_exists('site')) {
    function site()
    {
        return app('site')->current();
    }
}

if (!function_exists('size_format')) {
    function size_format($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
