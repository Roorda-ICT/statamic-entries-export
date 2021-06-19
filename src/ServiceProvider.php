<?php

declare(strict_types=1);

namespace RoordaIct\EntriesExport;

use Illuminate\Support\Facades\Route;
use RoordaIct\EntriesExport\Actions\ExportAction;
use RoordaIct\EntriesExport\Http\Controllers\ExportController;
use Statamic\Facades\Utility;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'entries-export';
    protected $actions = [ExportAction::class];

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/entries-export.php',
            'entries-export'
        );

        parent::register();
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/entries-export.php' => config_path('entries-export.php'),
        ]);

        $this->app->booted(function () {
            Utility::make('entries-export')
                ->icon('list')
                ->title('Export entries')
                ->navTitle('Export')
                ->description('Export an entire collection to Excel format.')
                ->routes(function ($router) {
                    $router->get('/', [ExportController::class, 'index'])->name('index');
                    $router->post('/', [ExportController::class, 'download'])->name('download');
                })
                ->register();
        });

        parent::boot();
    }
}
