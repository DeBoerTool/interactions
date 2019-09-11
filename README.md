# Interaction

## Installation

You can install the package via composer:

```bash
composer require dbt/interactions
```

You can publish the migration with:

```bash
php artisan vendor:publish --provider="Dbt\Interaction\InteractionServiceProvider" --tag="dbt-interactions-migration"
```

After publishing the migration you can create an interaction log table using 

```bash
php artisan migrate
```

You can publish interaction's config file using:
```bash
php artisan vendor:publish --provider="Dbt\Interaction\InteractionServiceProvider" --tag="dbt-interactions-config"
```

## Usage



## Etc.

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
