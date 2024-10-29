<?php

return [
    'breadcrumb' => 'Historial',

    'title' => 'Historial :record',

    'default_datetime_format' => 'd/m/Y, H:i:s',

    'table' => [
        'field' => 'Campo',
        'old' => 'Anterior',
        'new' => 'Nuevo',
        'restore' => 'Restaurar',
    ],

    'events' => [
        'updated' => 'Actualizado',
        'created' => 'Creado',
        'deleted' => 'Eliminado',
        'restored' => 'Restaurado',
        'restore_successful' => 'Restauración exitosa',
        'restore_failed' => 'Restauración fallida',
    ],
];
