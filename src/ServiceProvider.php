<?php

declare(strict_types=1);

namespace RoordaIct\EntriesExport;

use Illuminate\Support\Facades\Route;
use Statamic\Facades\Utility;
use Statamic\Providers\AddonServiceProvider;
use RoordaIct\EntriesExport\Actions\ExportAction;

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

        $this->app->booted(function() {
            Utility::make('entries-export')
                ->icon('list')
                ->title('Export entries')
                ->navTitle('Export')
                ->description('Export an entire collection to Excel format.')
                ->register();
        });

        parent::boot();
    }
}
