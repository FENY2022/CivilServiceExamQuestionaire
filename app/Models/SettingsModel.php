<?php

namespace App\Models;

class SettingsModel extends JsonFileModel
{
    public function __construct()
    {
        parent::__construct('settings.json', ['guest_mode' => false]);
    }

    public function isGuestMode(): bool
    {
        return (bool)($this->all()['guest_mode'] ?? false);
    }

    public function toggleGuestMode(): void
    {
        $settings = $this->all();
        $settings['guest_mode'] = !$this->isGuestMode();
        $this->write($settings);
    }
}
