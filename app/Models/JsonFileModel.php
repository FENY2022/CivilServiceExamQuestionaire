<?php

namespace App\Models;

class JsonFileModel
{
    protected string $fileName;
    protected array $defaultData = [];

    public function __construct(string $fileName, array $defaultData = [])
    {
        $this->fileName = $fileName;
        $this->defaultData = $defaultData;
        $this->ensureFile();
    }

    public function all(): array
    {
        $content = file_get_contents($this->path());
        $data = json_decode($content ?: '[]', true);
        return is_array($data) ? $data : $this->defaultData;
    }

    public function write(array $data): void
    {
        $this->ensureFile();
        file_put_contents($this->path(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    protected function path(): string
    {
        return WRITEPATH . 'data' . DIRECTORY_SEPARATOR . $this->fileName;
    }

    protected function ensureFile(): void
    {
        $dir = WRITEPATH . 'data';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $path = $this->path();
        if (file_exists($path)) {
            return;
        }

        $legacyPath = ROOTPATH . 'data' . DIRECTORY_SEPARATOR . $this->fileName;
        if (file_exists($legacyPath)) {
            copy($legacyPath, $path);
            return;
        }

        file_put_contents($path, json_encode($this->defaultData, JSON_PRETTY_PRINT));
    }
}
