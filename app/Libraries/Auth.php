<?php

namespace App\Libraries;

use App\Models\SettingsModel;
use App\Models\UserModel;

class Auth
{
    public function requireLogin()
    {
        $session = session();
        if (!$session->has('user') && !$session->has('admin') && !$session->has('guest')) {
            return redirect()->to('/login');
        }

        if ($session->has('guest')) {
            if (!(new SettingsModel())->isGuestMode()) {
                $session->remove('guest');
                return redirect()->to('/login');
            }
            return null;
        }

        if ($session->has('user')) {
            $sessionUser = $session->get('user');
            $users = new UserModel();
            $currentUser = null;
            foreach ($users->all() as $user) {
                if (($user['id'] ?? '') === ($sessionUser['id'] ?? '')) {
                    $currentUser = $user;
                    break;
                }
            }

            if (!$currentUser || $users->statusOf($currentUser) !== 'confirmed') {
                $session->remove('user');
                return redirect()->to('/login');
            }

            $session->set('user', $currentUser);
        }

        return null;
    }

    public function currentUserName(): string
    {
        $session = session();
        if ($session->has('admin')) {
            return 'Administrator';
        }
        if ($session->has('guest')) {
            return $session->get('guest.nickname') ?? 'Guest';
        }
        return $session->get('user.name') ?? 'Reviewer';
    }
}
