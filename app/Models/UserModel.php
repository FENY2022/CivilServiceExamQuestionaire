<?php

namespace App\Models;

class UserModel extends JsonFileModel
{
    public function __construct()
    {
        parent::__construct('users.json', []);
    }

    public function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));
        foreach ($this->all() as $user) {
            if (strtolower(trim($user['email'] ?? '')) === $email) {
                return $user;
            }
        }
        return null;
    }

    public function create(array $user): void
    {
        $users = $this->all();
        $users[] = $user;
        $this->write($users);
    }

    public function updateStatus(string $id, string $status): void
    {
        $users = $this->all();
        foreach ($users as &$user) {
            if (($user['id'] ?? '') !== $id) {
                continue;
            }
            $user['status'] = $status;
            if ($status === 'confirmed') {
                $user['confirmed_at'] = $user['confirmed_at'] ?? date('c');
                $user['disabled_at'] = null;
            }
            if ($status === 'disabled') {
                $user['disabled_at'] = date('c');
            }
        }
        unset($user);
        $this->write(array_values($users));
    }

    public function deleteById(string $id): void
    {
        $users = array_filter($this->all(), static fn (array $user): bool => ($user['id'] ?? '') !== $id);
        $this->write(array_values($users));
    }

    public function statusOf(array $user): string
    {
        return $user['status'] ?? (!empty($user['confirmed']) ? 'confirmed' : 'pending');
    }
}
