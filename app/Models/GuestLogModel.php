<?php

namespace App\Models;

class GuestLogModel extends JsonFileModel
{
    public function __construct()
    {
        parent::__construct('guest_logs');
    }

    public function all(): array
    {
        return $this->db()->query('SELECT * FROM guest_logs ORDER BY accessed_at DESC')->fetchAll();
    }

    public function log(string $nickname): void
    {
        $stmt = $this->db()->prepare('INSERT INTO guest_logs (id, nickname, accessed_at) VALUES (?, ?, ?)');
        $stmt->execute([bin2hex(random_bytes(8)), $nickname, date('c')]);
    }
}
