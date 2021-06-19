<?php

declare(strict_types=1);

namespace RoordaIct\EntriesExport\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Statamic\Facades\Collection;
use RoordaIct\EntriesExport\Exports\EntryCollectionExport;

class ExportController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('access entries-export utility');

        return view('entries-export::export', [
            'collections' => Collection::all(),
        ]);
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws ValidationException
     */
    public function download(Request $request)
    {
        $this->authorize('access entries-export utility');

        $collection = Collection::find($request->input('collection'));

        if (!$collection) {
            throw ValidationException::withMessages([
                'collection' => __('Please choose a valid collection.'),
            ]);
        }

        /** @var EntryCollectionExport $export */
        $export = app(config('entries-export.exporter'));

        return $export
            ->setItems($collection->queryEntries()->get())
            ->download($export->getFileName());
    }
}
