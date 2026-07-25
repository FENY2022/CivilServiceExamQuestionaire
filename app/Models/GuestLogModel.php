<?php

namespace App\Models;

class GuestLogModel extends JsonFileModel
{
    public function __construct()
    {
        parent::__construct('guest_logs.json', []);
    }

    public function log(string $nickname): void
    {
        $logs = $this->all();
        $logs[] = [
            'id' => bin2hex(random_bytes(8)),
            'nickname' => $nickname,
            'accessed_at' => date('c'),
        ];
        $this->write($logs);
    }
}
