<?php

namespace App\Admin\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Notifications\Notification;
use SolutionForest\FilamentTree\Actions\Action;
use SolutionForest\FilamentTree\Actions\ActionGroup;
use SolutionForest\FilamentTree\Actions\DeleteAction;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Actions\ViewAction;
use SolutionForest\FilamentTree\Widgets\Tree as BaseWidget;

class CategoryWidget extends BaseWidget
{
    protected static string $model = Category::class;

    protected static int $maxDepth = 2;

    protected ?string $treeTitle = 'CategoryWidget';

    protected bool $enableTreeTitle = true;

    protected function getFormSchema(): array
    {
        return [
            //
        ];
    }

    // INFOLIST, CAN DELETE
    public function getViewFormSchema(): array {
        return [
            //
        ];
    }
}