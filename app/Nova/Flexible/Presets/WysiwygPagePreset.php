<?php

namespace App\Nova\Flexible\Presets;

use App\PageBlocks;
use Whitecube\NovaFlexibleContent\Flexible;
use Whitecube\NovaFlexibleContent\Layouts\Preset;

class WysiwygPagePreset extends Preset
{

    /**
     * The available blocks
     *
     * @var Illuminate\Support\Collection
     */
    protected $blocks;

    /**
     * Create a new preset instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->blocks = PageBlocks::orderBy('label')->get();
    }

    /**
     * Execute the preset configuration
     *
     * @return void
     */
    public function handle(Flexible $field)
    {
        $field->button('Add new block');
        $field->resolver(\App\Nova\Flexible\Resolvers\WysiwygPageResolver::class);
        $field->help('Go to the "<strong>Page blocks</strong>" Resource in order to add new WYSIWYG block types.');

        $this->blocks->each(function($block) use ($field) {
            $field->addLayout($block->title, $block->id, $block->getLayoutFields());
        });
    }
}