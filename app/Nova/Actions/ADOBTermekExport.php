<?php

namespace App\Nova\Actions;

use App\Imports\ADOBProductsExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class ADOBTermekExport extends Action
{
  use InteractsWithQueue, Queueable;

  /**
   *
   */
  public $name = 'Termékek exportálása (ADOB)';


  /**
   * Perform the action on the given models.
   *
   * @param  \Laravel\Nova\Fields\ActionFields  $fields
   * @param  \Illuminate\Support\Collection  $models
   * @return mixed
   */
  public function handle(ActionFields $fields, Collection $models)
  {
    /** The name of the file to export data into...
     * @var string
     */
    $file = 'ADOB_termek-export-' . date('Y-m-d H_i_s') . '.xlsx';

    if (Excel::store(new ADOBProductsExport(request()->user(), null), "public/exports/{$file}")) {
      /** Laravel Nova download action.
       */
      return Action::download(Storage::url("{$file}"), $file);
    }
  }

  /**
   * Get the fields available on the action.
   *
   * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
   * @return array
   */
  public function fields(NovaRequest $request)
  {
    return [];
  }
}
