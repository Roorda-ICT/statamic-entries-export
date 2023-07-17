<?php

declare(strict_types=1);

namespace RoordaIct\EntriesExport\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Statamic\Entries\Collection;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection as CollectionFacade;
use RoordaIct\EntriesExport\Exports\EntryCollectionExport;

class ExportController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('access entries-export utility');

        $collections = CollectionFacade::all()
            ->reject(fn(Collection $collection) => in_array(
                $collection->handle(),
                config('entries-export.excluded_collections', [])
            ))
            ->filter(fn(Collection $collection) => Gate::allows(
                sprintf('%s %s entries', config('entries-export.permission'), $collection->handle()),
                $collection
            ));

        return view('entries-export::export', [
            'collections' => $collections,
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

        $collection = CollectionFacade::find($request->input('collection'));

        if (!$collection) {
            throw ValidationException::withMessages([
                'collection' => __('Please choose a valid collection.'),
            ]);
        }

        if (in_array($collection->handle(), config('entries-export.excluded_collections', []))) {
            throw ValidationException::withMessages([
                'collection' => __('You can not export entries from this collection.'),
            ]);
        }

        $entries = $collection->queryEntries()->get();

        $allowedEntries = $entries->filter(
            fn(Entry $entry) => Gate::allows(config('entries-export.permission'), $entry)
        );

        if ($allowedEntries->isEmpty()) {
            throw ValidationException::withMessages([
                'collection' => __('This collection does not have any entries you are allowed to export.'),
            ]);
        }

        /** @var EntryCollectionExport $export */
        $export = app(config('entries-export.exporter'));

        return $export
            ->setItems($allowedEntries)
            ->download($export->getFileName());
    }
}
