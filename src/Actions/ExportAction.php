<?php

declare(strict_types=1);

namespace RoordaIct\EntriesExport\Actions;

use Illuminate\Support\Collection;
use Statamic\Actions\Action;
use Statamic\Entries\Entry;
use RoordaIct\EntriesExport\Exports\EntryCollectionExport;

class ExportAction extends Action
{
    /**
     * Only allow exporting of entries.
     *
     * @param $item
     * @return bool
     */
    public function visibleTo($item)
    {
        return $item instanceof Entry;
    }

    /**
     * @param $user
     * @param $item
     * @return bool
     */
    public function authorize($user, $item): bool
    {
        return $user->can(config('entries-export.permission'), $item);
    }

    /**
     * Download items as CSV.
     *
     * @param Collection $items
     * @param $values
     * @return \Illuminate\Http\Response
     */
    public function download($items, $values)
    {
        /** @var EntryCollectionExport $export */
        $export = app(config('entries-export.exporter'));

        return $export
            ->setItems($items)
            ->download($export->getFileName());
    }

    public function buttonText()
    {
        /** @translation */
        return 'Export entry|Export :count entries';
    }

    public function warningText()
    {
        return 'Are you sure you want to export this entry?|Are you sure you want to export :count entries?';
    }
}
