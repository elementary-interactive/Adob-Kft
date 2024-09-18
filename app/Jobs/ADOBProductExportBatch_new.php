<?php

namespace App\Jobs;

use App\Exports\ADOBProductsExport_new;
use App\Models\ProductExport;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;

class ADOBProductExportBatch_new implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $logger;

    public function __construct(
        protected ProductExport $export
    )
    {
        $this->logger = new Logger('adob_exporter');
        $this->logger->pushHandler(new LogtailHandler('1sKmnmxToqZ5NPAJy6EfvyAZ'));
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            new WithoutOverlapping($this->export->id)
        ];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $this->exportStarted();

            $outputFile = storage_path('app/' . $this->export->file);
            $scriptPath = base_path('python/run.sh');

            $process = Process::run('sh ' . $scriptPath . ' ' . $outputFile);        //$process = Process::run('pwd');

            $this->logger->info('Export process', (array)$process->output());

            $this->exportFinished();

        } catch (\Exception $e) {
            $this->logger->info('Export error', (array)$e);
            dump($e);
        } catch (\Throwable $e) {
            $this->export->status = 'failed';
            $this->logger->info('Export error', (array)$e);
            dump($e);
        }
    }

    private function exportStarted()
    {
        $this->export->status = 'running';
        $this->export->save();

        Notification::make()
            ->title('Exportálás folyamata...')
            ->body(' Termékek exportálása...')
            ->info()
            ->sendToDatabase($this->export->exported_by);
    }

    public function exportFinished()
    {
        $this->export->status = 'finished';
        $this->export->finished_at = now();
        $this->export->save();

        Notification::make()
            ->title('Exportálás folyamata...')
            ->body((($this->export->fails_counter > 0) ? 'Végeztünk.' : 'Sikeresen végeztünk!') . ' A keresett állomány itt tölthető le: <a href="' . Storage::url($this->export->file) . '">' . $this->export->file . '</a>')
            ->success()
            ->sendToDatabase($this->export->exported_by);
    }
}
