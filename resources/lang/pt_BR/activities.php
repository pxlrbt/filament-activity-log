<?php

return [
    'breadcrumb' => 'Histórico',

    'title' => 'Histórico :record',

    'default_datetime_format' => 'd/m/Y H:i:s',

    'table' => [
        'field' => 'Campo',
        'old' => 'Antes',
        'new' => 'Depois',
        'restore' => 'Restaurado',
    ],

    'events' => [
        'updated' => 'Atualizado',
        'created' => 'Criado',
        'deleted' => 'Excluído',
        'restored' => 'Restaurado',
        'restore_successful' => 'Restaurado com sucesso',
        'restore_failed' => 'Falha na restauração',
    ],
];
