<?php

namespace App\Nova;

use App\Models\Columns\ADOBProductsImportColumns;
use App\Nova\Admin as NovaAdmin;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Neon\Admin\Models\Admin;

class ProductImport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ProductImport>
     */
    public static $model = \App\Models\ProductImport::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'created_at';

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Indicates whether the resource should automatically poll for new resources.
     *
     * @var bool
     */
    public static $polling = true;

    /**
     * The interval at which Nova should poll for new resources.
     *
     * @var int
     */
    public static $pollingInterval = 10;

    /**
     * The default shorting field.
     *
     * @var string
     */
    public static $defaultSort = 'created_at';

    /**
     * The default shorting direction.
     *
     * @var string
     */
    public static $defaultDir = 'desc';

    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;

    public static function label()
    {
        return __('Imports');
    }

    public static function singularLabel()
    {
        return __('Import');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $m = $this;

        return [
            Status::make('', 'status')
                ->loadingWhen(['waiting', 'running'])
                ->failedWhen(['failed']),
            Number::make('Összesen', 'records_counter'),
            Stack::make('Termékek', [
                Text::make('Beszúrva')->resolveUsing(function () {
                    return 'Beszúrva: '.$this->resource->products_inserted;
                }),
                Text::make('Módosítva')->resolveUsing(function () {
                    return 'Módosítva: '.$this->resource->products_modified;
                }),
            ]),
            
            Stack::make('Kategóriák', [
                Text::make('Beszúrva')->resolveUsing(function () {
                    return 'Beszúrva: '.$this->resource->categories_inserted;
                }),
                Text::make('Módosítva')->resolveUsing(function () {
                    return 'Módosítva: '.$this->resource->categories_modified;
                }),
            ]),

            Stack::make('Márkák', [
                Text::make('Beszúrva')->resolveUsing(function () {
                    return 'Beszúrva: '.$this->resource->brands_inserted;
                }),
                Text::make('Módosítva')->resolveUsing(function () {
                    return 'Módosítva: '.$this->resource->brands_modified;
                }),
            ]),

            Number::make(__('Fails counter'), 'fails_counter'),

            // Code::make('Adatforrás', 'data')
            //     ->hideFromIndex()
            //     ->showOnDetail()
            //     ->json(),

            Text::make('Hibaüzenetek', function() use ($m) {
                $batch  = Bus::findBatch($m->resource->batch_id);
                $jobs   = DB::table('failed_jobs')->whereIn('uuid', $batch->failedJobIds)->get();

                $result = '';
                foreach ($jobs as $job) {
                    $__job = unserialize(json_decode($job->payload)->data->command);
                    // $result .= '<strong>'.$__job->record()[ADOBProductsImportColumns::PRODUCT_ID->value].'</strong>: '.Str::limit($job->exception, 240)."<br/>";
                    $result .= Str::limit($job->exception, 240)."<br/>";
                }
                return $result;
            })
                ->asHtml()
                ->hideFromIndex(),

            // Stack::make('Hibaüzenetek', function() use ($m) {
            //     $batch  = Bus::findBatch($m->resource->batch_id);
            //     $jobs   = DB::table('failed_jobs')->whereIn('uuid', $batch->failedJobIds)->get();

            //     $result = [];
            //     foreach ($jobs as $index => $job) {
            //         $result[] = Text::make($index)->resolveUsing(function () use ($job) {
            //             $__job = unserialize(json_decode($job->payload)->data->command);
            //             return '<strong>'.$__job->record()[ADOBProductsImportColumns::PRODUCT_ID->value].'</strong>: '.Str::limit($job->exception, 240)."<br/>";
            //         })->asSmall();
            //     }
            //     return $result;
            // })
            //     ->hideFromIndex(),

            DateTime::make(__('Started at'), 'created_at')
                ->sortable(),
            DateTime::make(__('Finished at'), 'finished_at'),

            BelongsTo::make(__('Importer'), 'imported_by', NovaAdmin::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if (static::$defaultSort && empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            return $query->orderBy(static::$defaultSort, static::$defaultDir);
        }
        return $query;
    }
}
