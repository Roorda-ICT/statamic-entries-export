<?php

declare(strict_types=1);

return [
    /**
     * The permission necessary to be able to export the entry. This gets passed to the gate,
     * by default this would be the ability to 'view' the entry. For example you might only
     * want to let people export entries that can 'update' them.
     */
    'permission' => 'view',

    /**
     * The export format to which the entries should be exported.
     * Possible values: 'xlsx', 'csv', 'ods', 'html'
     *
     * See also: https://docs.laravel-excel.com/3.1/exports/export-formats.html
     */
    'export_format' => 'ods',

    /**
     * The handles of the field types that should be skipped when exporting.
     * If you need finer-grained control of this create a custom exporter.
     */
    'excluded_field_types' => ['section', 'hidden'],

    /**
     * The class responsible for converting the Entries into an array of values that get exported.
     * If you create your own implementation, make sure to extend the EntryCollectionExport.
     *
     * See also: https://docs.laravel-excel.com/3.1/exports
     */
    'exporter' => \RoordaIct\EntriesExport\Exports\EntryCollectionExport::class,
];
