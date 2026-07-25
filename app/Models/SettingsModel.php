<?php

namespace App\Models;

class SettingsModel extends JsonFileModel
{
    public function __construct()
    {
        parent::__construct('settings');
    }

    public function all(): array
    {
        $rows = $this->db()->query('SELECT key, value FROM settings')->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    public function isGuestMode(): bool
    {
        return ($this->all()['guest_mode'] ?? '0') === '1';
    }

    public function toggleGuestMode(): void
    {
        $current = $this->isGuestMode();
        $newVal = $current ? '0' : '1';

        $stmt = $this->db()->prepare('UPDATE settings SET value = ? WHERE key = \'guest_mode\'');
        $stmt->execute([$newVal]);
    }
}
