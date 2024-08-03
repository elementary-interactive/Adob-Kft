<?php
 
namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductImport;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ADOBNotifyJob implements ShouldQueue
{

  public function __construct(
    protected ProductImport $tracker
  )
  {
    
  }

  public function handle() {
    Notification::make()
    ->title('Importálás folyamata...')
    ->body('Kategóriák és képek ellenőrzése.')
    ->info()
    ->sendToDatabase($this->tracker->imported_by);
  }
}