<?php

namespace App\Models;

class UserModel extends JsonFileModel
{
    public function __construct()
    {
        parent::__construct('users');
    }

    public function all(): array
    {
        return $this->db()->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    }

    public function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));
        $stmt = $this->db()->prepare('SELECT * FROM users WHERE LOWER(TRIM(email)) = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $user): void
    {
        $stmt = $this->db()->prepare('INSERT INTO users (id, name, email, age, status, created_at, confirmed_at, disabled_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $user['id'] ?? '',
            $user['name'] ?? '',
            $user['email'] ?? '',
            (int) ($user['age'] ?? 0),
            $user['status'] ?? 'pending',
            $user['created_at'] ?? date('c'),
            $user['confirmed_at'] ?? null,
            $user['disabled_at'] ?? null,
        ]);
    }

    public function updateStatus(string $id, string $status): void
    {
        $confirmedAt = null;
        $disabledAt = null;

        if ($status === 'confirmed') {
            $existing = $this->findById($id);
            $confirmedAt = $existing['confirmed_at'] ?? date('c');
            $disabledAt = null;
        }
        if ($status === 'disabled') {
            $disabledAt = date('c');
        }

        $stmt = $this->db()->prepare('UPDATE users SET status = ?, confirmed_at = ?, disabled_at = ? WHERE id = ?');
        $stmt->execute([$status, $confirmedAt, $disabledAt, $id]);
    }

    public function deleteById(string $id): void
    {
        $stmt = $this->db()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function statusOf(array $user): string
    {
        return $user['status'] ?? (!empty($user['confirmed']) ? 'confirmed' : 'pending');
    }

    private function findById(string $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
