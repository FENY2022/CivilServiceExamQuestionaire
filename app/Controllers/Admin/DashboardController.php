<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuestLogModel;
use App\Models\SettingsModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->has('admin')) {
            return redirect()->to('/login');
        }

        $users = new UserModel();
        $allUsers = $users->all();
        $statusOf = static fn (array $user): string => $users->statusOf($user);

        return view('admin/index', [
            'title' => 'Admin Panel - ' . config('App')->appName,
            'active' => 'admin',
            'users' => $allUsers,
            'statusOf' => $statusOf,
            'pending' => count(array_filter($allUsers, static fn ($user) => $statusOf($user) === 'pending')),
            'confirmed' => count(array_filter($allUsers, static fn ($user) => $statusOf($user) === 'confirmed')),
            'disabled' => count(array_filter($allUsers, static fn ($user) => $statusOf($user) === 'disabled')),
            'guestMode' => (new SettingsModel())->isGuestMode(),
            'guestLogs' => array_reverse((new GuestLogModel())->all()),
        ]);
    }

    public function action()
    {
        if (!session()->has('admin')) {
            return redirect()->to('/login');
        }

        $action = $this->request->getPost('action') ?? '';
        if ($action === 'toggle_guest_mode') {
            (new SettingsModel())->toggleGuestMode();
            return redirect()->to('/admin');
        }

        $userId = (string)$this->request->getPost('user_id');
        $users = new UserModel();
        if ($action === 'delete') {
            $users->deleteById($userId);
        } elseif ($action === 'confirm' || $action === 'enable') {
            $users->updateStatus($userId, 'confirmed');
        } elseif ($action === 'disable') {
            $users->updateStatus($userId, 'disabled');
        }

        return redirect()->to('/admin');
    }
}
