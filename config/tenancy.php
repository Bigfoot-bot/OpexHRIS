<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use App\Models\Central\Tenant;

return [

    'tenant_model' => Tenant::class,

    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'central_domains' => array_filter([
        '127.0.0.1',
        'hris-platform.test',
        parse_url(env('APP_URL', ''), PHP_URL_HOST) ?: null,
    ]),

    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
    ],

    'database' => [
        'based_on_connection' => env('DB_CONNECTION', 'mysql'),
        'prefix' => '',
        'suffix' => '',
    ],

    'migration_parameters' => [
        '--force' => true,
    ],

    'seeder_parameters' => [
        '--force' => true,
    ],

];