<?php

namespace App\Nova\Actions;

use App\Exports\ADOBProductsExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

use Log;
use Validator;
use Excel;
use Config;
use Input;
use App\Models\Product;
use App\Models\ProductImport;
use Illuminate\Support\Facades\URL;
use stdClass;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ADOBProductsExportAction extends Action
{
  use InteractsWithQueue, Queueable;

  /**
   *
   */
  public $name = 'Termékek exportálása (ADOB)';

  protected $filename;

  public function __construct()
  {
    $this->filename = 'ADOB_termek-export-' . date('Y-m-d H_i_s') . '.xlsx';
  }


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
    $file = $this->getFilename(); // 'ADOB_termek-export-' . date('Y-m-d H_i_s') . '.xlsx';

    $response = Excel::download((new ADOBProductsExport(request()->user(), null)), $file);

    if (!$response instanceof BinaryFileResponse || $response->isInvalid()) {
      return Action::danger(__('Resource could not be exported.'));
    }

    return Action::download(
      $this->getDownloadUrl($response->getFile()->getPathname()),
      $file
    );
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

  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * @param  string  $filePath
   * @return string
   */
  protected function getDownloadUrl(string $filePath): string
  {
    return URL::temporarySignedRoute('export.download', now()->addMinutes(1), [
      'path'     => encrypt($filePath),
      'filename' => $this->getFilename(),
    ]);
  }
}
