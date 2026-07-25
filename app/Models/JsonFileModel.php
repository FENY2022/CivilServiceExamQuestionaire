<?php

namespace App\Models;

class JsonFileModel
{
    protected string $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    protected function db(): \PDO
    {
        return \DB::connect();
    }
}
