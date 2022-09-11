<?php

function databaseConfig(): array
{
    return [
        'sqlite' =>
            [
                'DATABASE_URL' => "sqlite:" . __DIR__ . '/blogbase.sqlite'
            ],
    ];
}