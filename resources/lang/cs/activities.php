<?php

return [
    'breadcrumb' => 'Log',

    'title' => 'Log entity ":record"',

    'default_datetime_format' => 'j.n.Y H:i:s',

    'table' => [
        'field' => 'Pole',
        'old' => 'Původní',
        'new' => 'Nové',
        'restore' => 'Obnovit',
    ],

    'events' => [
        'updated' => 'Upraveno',
        'created' => 'Vytvořeno',
        'deleted' => 'Smazáno',
        'restored' => 'Obnoveno',
        'restore_successful' => 'Úspěšně obnoveno',
        'restore_failed' => 'Obnovení selhalo',
    ],
];
