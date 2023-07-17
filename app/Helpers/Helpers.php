<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

if (!function_exists('block_template'))
{
    function block_template($block): array
    {
        $template = 'block';

        $attrs = $block->getAttributes();

        $result = [];

        if (array_key_exists('template', $attrs) && !empty($attrs['template']))
        {
            $result[] = Arr::first(app('site')->current()->domains).".partials.{$block->name()}.{$attrs['template']}";
            $result[] = "partials.{$block->name()}.{$attrs['template']}";
        }

        $result[] = Arr::first(app('site')->current()->domains).".partials.{$block->name()}.block";
        $result[] = "partials.{$block->name()}.block";

        // dump($result);

        return $result;
    }
}