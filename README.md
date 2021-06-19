![Banner](https://raw.githubusercontent.com/roorda-ict/statamic-entries-export/master/banner.png)

This is a simple package that exports your entries to xlsx/csv/ods/html format.

## Features
- Exporting entries from the collection listings.
- Exporting a full collection from the utility page.
- Tries to export to a human-readable format. This means that linked Entries will not export id's but their titles, the same goes for linked users etc.

## Installation
To install this addon, use composer to require the package in your project:

```bash
composer require roorda-ict/statamic-entries-export
```

## Usage
Usage is pretty easy:
- **For a subset of entries**: navigate to a collection listing and start exporting entries.
- **For an entire collection of entries**: go to Utilities > Export entries, choose your collection and click the 'Export entries' button.

## Configuration
If you want to update any of the defaults, start by publishing your configuration file:

```bash
php artisan vendor:publish --provider=RoordaIct\\EntriesExport\\ServiceProvider
```

This will publish the `entries-export.php` file to the `config` directory.
This is the file you need to update to make any configuration changes.

### More granular export permission
By default, users that have the 'Utilities > Export entries' permission can export the entries that they may `view`.
If you want to make this stricter, you can update the config and set it to whatever you like.
For example, you might only want to let users export the entries that they may `update`.

To do this you update the value of the `permission` key from `view` to `update` in the configuration file.

### Export format
By default, we export to the `xlsx` format, however you can configure this to any supported format.
The supported formats are found in the configuration file, at the `export_format` key.

### Excluding field types
By default, we do not export the `section` and `hidden` field types.
If you need to add or remove field types, update the value of the `excluded_field_types` key in the configuration file.

### Custom exporter
If you want more control in how your export is generated, you can create your own exporter.
Make sure to extend the `RoordaIct\EntriesExport\Exports\EntryCollectionExport` class.

The heavy lifting of creating the Excel sheet is done through the Laravel Excel library. 
If you need more support on how to create your own exportable, please [refer to their documentation](https://docs.laravel-excel.com/3.1/exports/). 

After you created your own exporter, you can update the value of the `exporter` key in the configuration file.
