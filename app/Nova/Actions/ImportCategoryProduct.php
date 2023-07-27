<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

use Log;
use Validator;
use Excel;
use Config;
use Input;
use App\Models\Product;
use stdClass;


class ImportCategoryProduct extends Action
{
  use InteractsWithQueue, Queueable;

  const MAX_NUMBER_OF_ALTERNATIVE_IMG = 10;
  const HEADER_ROW_INDEX = 1;

  protected $logger;
  private $headerErrors = [];
  private $requiredColLabels = [
    'PRODUCT_ID' => 'PID',
    'IMAGE_PRIMARY_LINK' => 'IMGURL',
    'IMAGE_ENERGY_LABEL' => 'IMGURL_ENERGY_LABEL',
    'IMGURL_PACK_01' => 'IMGURL_PACK_01',
    'IMGURL_PACK_02' => 'IMGURL_PACK_02',
    'IMGURL_ALTERNATIVE' => 'IMGURL_ALTERNATIVE_', // IMGURL_ALTERNATIVE_01 ... IMGURL_ALTERNATIVE_24
  ];


  private $optionalColLabels = [
    'PRODUCT_ID2' => 'PRODUCTNO2',
    'DATA_SHEET_HUN' => 'ATTACH_INFORMATION_SHEET_HUN',
  ];

  private $headerWarnings = [];

  /**
   *
   */
  public $name = 'Termékek importálása';

  /**
   * Perform the action on the given models.
   *
   * @param  \Laravel\Nova\Fields\ActionFields  $fields
   * @param  \Illuminate\Support\Collection  $models
   * @return mixed
   */
  public function handle(ActionFields $fields, Collection $models)
  {
    $rules = array(
      'file' => 'required|mimes:xls,xlsx'
    );

    $validator = Validator::make($fields, $rules);

    if ($validator->fails()) {
      return Action::danger($validator->errors);
    } else {

      config([
        'excel.import.startRow'   => self::HEADER_ROW_INDEX,
        'excel.import.heading'    => 'original',
        'excel.import.calculate'  => false,
      ]);

      Excel::load($fields->file, function ($reader) {

        $headerValidations = $this->checkHeaderOkayOld($reader);

        if (count($headerValidations->errors) > 0) {
          $this->headerErrors = array_merge($this->headerErrors, $headerValidations->errors);
          $this->headerWarnings = array_merge($this->headerWarnings, $headerValidations->warnings);

          return 0; // stop processing the file
        } else {

          $sheetIndex = 0;

          foreach ($reader->toArray() as $sheet) {

            $rowIndex = 0;

            foreach ($sheet as $row) {
              $this->saveProduct($row);
              $rowIndex += 1;
            }

            $sheetIndex += 1;
          }
        }
      }, 'UTF-8');


      if (count($this->headerErrors) > 0) {
        return response()->json(
          [
            'errors' => ['header' => $this->headerErrors],
            'warnings' => ['header' => $this->headerWarnings]
          ],
          422,
          ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
          JSON_UNESCAPED_UNICODE
        );
      }

      return response()->json(
        [
          'success' => 'Sikeres feltöltés!'
        ],
        200,
        ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE
      );
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
    return [
      File::make("File", 'file')
        ->rules('required', 'mimes:xls,xlsx'),
      Number::make('Fejléc sor', 'header_row')
        ->min(0)
        ->step(1)
        ->withMeta([
          "defaultValue" => 0,
        ]),
    ];
  }

  private function checkHeaderOkayOld($reader)
  {
    $validator = new stdClass();
    $validator->errors = [];
    $validator->warnings = [];

    $rows = $reader->toArray();

    if (!empty($rows[0])) {
      $headerAttrs = array_keys($rows[0][0]);
      $this->validateHeader($headerAttrs, 0, $validator);
    } else {
      array_push($validator->errors, 'Nincs fejléc');
    }
    return $validator;
  }

  private function validateHeader($cols, $sheetIndex, &$validator)
  {
    if (!$validator) {
      $validator = new stdClass();
      $validator->errors = [];
      $validator->warnings = [];
    }

    if (is_array($cols) && count($cols) == 0) {
      array_push($validator->errors, "A megadott sorban nincsenek oszlop megnevezések.");
    }

    if (!in_array($this->requiredColLabels['PRODUCT_ID'], $cols, true)) {
      array_push($validator->errors, "Nem található '" . $this->requiredColLabels['PRODUCT_ID'] . "' oszlop.");
    }

    if (!in_array($this->requiredColLabels['IMAGE_PRIMARY_LINK'], $cols, true)) {
      array_push($validator->errors, "Nem található '" . $this->requiredColLabels['IMAGE_PRIMARY_LINK'] . "' oszlop.");
    }

    if (!in_array($this->requiredColLabels['IMAGE_ENERGY_LABEL'], $cols, true)) {
      array_push($validator->warnings, "Nem található '" . $this->requiredColLabels['IMAGE_ENERGY_LABEL'] . "' oszlop.");
    }

    if (!in_array($this->requiredColLabels['IMGURL_PACK_01'], $cols, true)) {
      array_push($validator->warnings, "Nem található '" . $this->requiredColLabels['IMGURL_PACK_01'] . "' oszlop.");
    }
    if (!in_array($this->requiredColLabels['IMGURL_PACK_02'], $cols, true)) {
      array_push($validator->warnings, "Nem található '" . $this->requiredColLabels['IMGURL_PACK_02'] . "' oszlop.");
    }

    // check for IMGURL_ALTERNATIVE_01..24 cols
    for ($i = 1; $i <= self::MAX_NUMBER_OF_ALTERNATIVE_IMG; $i++) {
      $index = $i < 10 ? '0' . $i : $i;

      if (!in_array($this->requiredColLabels['IMGURL_ALTERNATIVE'] . $index, $cols, true)) {
        array_push($validator->errors, "Nem található '" . $this->requiredColLabels['IMGURL_ALTERNATIVE'] . $index . "' oszlop.");
      }
    }

    $add = function ($e) use ($sheetIndex) {
      return $e . 'a ' . $sheetIndex . '. munkafüzetbenben';
    };

    $validator->errors = array_map($add, $validator->errors);

    return $validator;
  }

  private function saveProduct($row)
  {
    $pid = $row[$this->requiredColLabels['PRODUCT_ID']];
    $product = Product::where('product_id', '=', $pid)->first();

    if ($product) {
      $this->logger->incrementCounter(2);
      $productMediaCount = $product->getMediaCount('images');

      if ($productMediaCount == 0) {
        // add all images to product

        //add primary image
        $this->addImage($row[$this->requiredColLabels['IMAGE_PRIMARY_LINK']], $product);

        //add alternative images
        $this->addAltImages($row, $product);
        $this->addImage($row[$this->requiredColLabels['IMGURL_PACK_01']], $product);
        $this->addImage($row[$this->requiredColLabels['IMGURL_PACK_02']], $product);
        $this->addImage($row[$this->requiredColLabels['IMAGE_ENERGY_LABEL']], $product);
      }

      if ($productMediaCount == 1) {
        //add alternative images
        $this->addAltImages($row, $product);
        $this->addImage($row[$this->requiredColLabels['IMGURL_PACK_01']], $product);
        $this->addImage($row[$this->requiredColLabels['IMGURL_PACK_02']], $product);
        $this->addImage($row[$this->requiredColLabels['IMAGE_ENERGY_LABEL']], $product);
      }

      if ($productMediaCount > 1 && $productMediaCount < 5) {
        //add alternative images
        $this->addAltImages($row, $product, 5);
        $this->addImage($row[$this->requiredColLabels['IMGURL_PACK_01']], $product);
        $this->addImage($row[$this->requiredColLabels['IMGURL_PACK_02']], $product);
        $this->addImage($row[$this->requiredColLabels['IMAGE_ENERGY_LABEL']], $product);
      }
    }
  }

  /**
   * @param $row
   * @param $product
   */
  private function addImage($link, $product)
  {
    // if (!is_null($link)) {
    //   $job = new AddMediaFromUrl($link, $product->id);
    //   Log::info("$product->id , $link");
    //   dispatch($job);
    //   $this->logger->incrementCounter(1);
    // }
  }

  /**
   * @param $row
   * @param $product
   */
  private function addAltImages($row, $product, $fromIndex = 1)
  {
    for ($i = $fromIndex; $i <= self::MAX_NUMBER_OF_ALTERNATIVE_IMG; $i++) {
      $index = $i < 10 ? '0' . $i : $i;
      $this->addImage(
        $row[$this->requiredColLabels['IMGURL_ALTERNATIVE'] . $index],
        $product
      );
    }
  }
}
