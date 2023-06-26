<?php

declare(strict_types=1);

namespace RoordaIct\EntriesExport\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Statamic\Auth\User;
use Statamic\Contracts\Assets\Asset;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Contracts\Query\Builder as BuilderContract;
use Statamic\Contracts\Taxonomies\Term;
use Statamic\Entries\Entry;
use Statamic\Entries\EntryCollection;
use Statamic\Fields\Field;
use Statamic\Fields\Value;
use Statamic\Fields\LabeledValue;

class EntryCollectionExport implements FromCollection, WithHeadings
{
    use Exportable;

    private Collection $collection;

    /**
     * Set the collection of entries that is about to be exported
     *
     * @param Collection $items
     * @return self
     */
    public function setItems(Collection $items): EntryCollectionExport
    {
        $notAllEntries = $items->some(fn($entry) => !($entry instanceof EntryContract));

        if ($notAllEntries) {
            throw new \InvalidArgumentException('Collection export expects a collection of entries.');
        }

        $this->collection = $items;

        return $this;
    }

    /**
     * Get the fields that should be included in the export for this collection
     *
     * @return Collection
     */
    public function fields(): Collection
    {
        /** @var Entry $entry */
        $entry = $this->collection->first();

        return $entry->blueprint()
            ->fields()
            ->all()
            ->filter(fn(Field $field) => $this->shouldFieldBeIncluded($field));
    }

    /**
     * Get the headings for the export
     *
     * @return array
     */
    public function headings(): array
    {
        return $this->fields()
            ->map->display()
            ->all();
    }

    /**
     * Transform the collection of entries into a collection of export rows.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        // Get all heading handles.
        $headings = $this->fields()->keys();

        // Transform every entry into an array with the entry values.
        return $this->collection->map(function (EntryContract $entry) use ($headings) {
            // Map every heading to the corresponding value for this entry.
            return $headings->map(function ($heading) use ($entry) {
                $value = $entry->augmentedValue($heading);

                return $this->toString($value);
            });
        });
    }

    /**
     * Convert an augmented entry field value to a string so it can be put in the Excel column
     *
     * @param $value
     * @return bool|float|int|string|null
     */
    private function toString($value)
    {
        // It's an augmented value, unpack it, so we can use it.
        if ($value instanceof Value) {
            $value = $value->value();
        }

        if ($value instanceof BuilderContract) {
            $value = $value->get();
        }

        if ($value instanceof EntryCollection) {
            return $value
                ->map(fn(EntryContract $entry) => $entry->get('title'))
                ->join(', ');
        }

        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof Carbon) {
            return $value->format('d-m-Y H:i');
        }

        if ($value instanceof EntryContract) {
            return $value->get('title');
        }

        if ($value instanceof User) {
            return $value->name();
        }

        if ($value instanceof Term) {
            return $value->title();
        }

        if ($value instanceof LabeledValue) {
            return $value->label();
        }

        if ($value instanceof Asset) {
            return $value->url();
        }

        if (is_null($value)) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        if (is_numeric($value)) {
            return $value;
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        Log::warning('[EntryCollectionExport] unhandled value', ['value' => $value, 'type' => gettype($value)]);

        return $value;
    }

    /**
     * Whether this specific field should be included in the export
     *
     * @param Field $field
     * @return bool
     */
    protected function shouldFieldBeIncluded(Field $field): bool
    {
        return !in_array($field->type(), config('entries-export.excluded_field_types'));
    }

    /**
     * Get the filename of the export
     *
     * @return string
     */
    public function getFileName(): string
    {
        /** @var EntryContract $entry */
        $entry = $this->collection->first();
        $format = config('entries-export.export_format');

        return sprintf(
            '%s export %s.%s',
            $entry->collection()->title(),
            Carbon::now()->toDateTimeString(),
            $format,
        );
    }
}
