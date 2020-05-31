<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the exporter. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */
    'disk' => env('RESOURCE_EXPORTER_DISK', env('FILESYSTEM_DRIVER', 'local')),
    /*
    |--------------------------------------------------------------------------
    | Resource Payload
    |--------------------------------------------------------------------------
    |
    | Here you may specify if your resource payload has a different format.
    | This configuration avoid you to use ->withBootstrapThree on every request.
    |
    | Supported Payloads: "default", "bootstrap3"
    |
    */
    'payload' => env('RESOURCE_EXPORTER_PAYLOAD', 'default'),
];
