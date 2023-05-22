![header](./.github/resources/header.png)

# Filament Activity Log

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pxlrbt/filament-activity-log.svg?include_prereleases)](https://packagist.org/packages/pxlrbt/filament-activity-log)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/pxlrbt/filament-activity-log/code-style.yml?branch=main)
[![Total Downloads](https://img.shields.io/packagist/dt/pxlrbt/filament-activity-log.svg)](https://packagist.org/packages/pxlrbt/filament-activity-log)

This package adds a page to the Filament Admin panel to view the activity log.

## Installation

Install via Composer.

**Requires PHP 8.0 and Filament 2.0**

```bash
composer require pxlrbt/filament-activity-log
```

## Usage

### Create a page

Create the page inside your resources `Pages/` directory. Replace `ProductResource` with your resource. 

```php
<?php

namespace App\Filament\Resources\ProductResource\Pages;

use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListProductActivites extends ListActivities
{
    protected static string $resource = ProductResource::class;
}
```

### Register the page

Add the page to your resource's `getPages()` method.

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListSpecifications::route('/'),
        'create' => Pages\CreateSpecification::route('/create'),
        'activities' => Pages\ListSpecificationActivites::route('/{record}/activities'),
        'edit' => Pages\EditSpecification::route('/{record}/edit'),
    ];
}
```

## Contributing

If you want to contribute to this packages, you may want to test it in a real Filament project:

- Fork this repository to your GitHub account.
- Create a Filament app locally.
- Clone your fork in your Filament app's root directory.
- In the `/filament-activity-log` directory, create a branch for your fix, e.g. `fix/error-message`.

Install the packages in your app's `composer.json`:

```json
"require": {
    "pxlrbt/filament-activity-log": "dev-fix/error-message as main-dev",
},
"repositories": [
    {
        "type": "path",
        "url": "filament-activity-log"
    }
]
```

Now, run `composer update`.
