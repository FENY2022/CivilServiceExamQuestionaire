<?php

namespace App\Controllers;

use App\Models\GuestLogModel;
use App\Models\SettingsModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        return view('auth/login', [
            'title' => 'Login - ' . config('App')->appName,
            'active' => 'login',
            'defaultTab' => $this->request->getGet('admin') ? 'admin' : ($this->request->getGet('guest') ? 'guest' : 'user'),
            'guestMode' => (new SettingsModel())->isGuestMode(),
            'message' => session()->getFlashdata('message'),
            'status' => session()->getFlashdata('status') ?? 'info',
        ]);
    }

    public function loginAction()
    {
        $mode = $this->request->getPost('mode') ?? 'user';
        $session = session();

        if ($mode === 'guest') {
            $nickname = trim((string)$this->request->getPost('nickname'));
            $settings = new SettingsModel();
            if (!$settings->isGuestMode()) {
                return $this->withFlash('/login?guest=1', 'Guest access is currently disabled.', 'error');
            }
            if ($nickname === '') {
                return $this->withFlash('/login?guest=1', 'Please enter your nickname.', 'error');
            }
            (new GuestLogModel())->log($nickname);
            $session->set('guest', ['nickname' => $nickname, 'started_at' => date('c')]);
            $session->remove(['user', 'admin']);
            return redirect()->to('/dashboard');
        }

        if ($mode === 'admin') {
            $app = config('App');
            if (trim((string)$this->request->getPost('username')) === $app->adminUsername && trim((string)$this->request->getPost('password')) === $app->adminPassword) {
                $session->set('admin', true);
                $session->remove(['user', 'guest']);
                return redirect()->to('/admin');
            }
            return $this->withFlash('/login?admin=1', 'Invalid admin credentials.', 'error');
        }

        $email = strtolower(trim((string)$this->request->getPost('email')));
        $users = new UserModel();
        $user = $users->findByEmail($email);
        if (!$user) {
            return $this->withFlash('/login', 'No account found with that email address. Please register first.', 'error');
        }

        $accountStatus = $users->statusOf($user);
        if ($accountStatus === 'pending') {
            return $this->withFlash('/login', 'Your account is pending admin approval.', 'warning');
        }
        if ($accountStatus === 'disabled') {
            return $this->withFlash('/login', 'Your account has been disabled. Please contact the admin.', 'error');
        }

        $session->set('user', $user);
        $session->remove(['admin', 'guest']);
        return redirect()->to('/dashboard');
    }

    public function register()
    {
        if ((new SettingsModel())->isGuestMode()) {
            return $this->withFlash('/', 'Guest Mode is enabled. Registration is closed for now.', 'warning');
        }

        $name = trim((string)$this->request->getPost('name'));
        $email = strtolower(trim((string)$this->request->getPost('email')));
        $age = trim((string)$this->request->getPost('age'));
        $captcha = (int)($this->request->getPost('captcha') ?? 0);
        $expectedCaptcha = (int)(session()->get('captcha_answer') ?? -1);
        $users = new UserModel();

        if ($name === '') {
            return $this->withFlash('/', 'Please enter your full name.', 'error');
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->withFlash('/', 'Please enter a valid email address.', 'error');
        }
        if ($age === '' || !ctype_digit($age) || (int)$age < 1) {
            return $this->withFlash('/', 'Please enter a valid age.', 'error');
        }
        if ($captcha !== $expectedCaptcha) {
            return $this->withFlash('/', 'Incorrect CAPTCHA answer. Please solve the math question again.', 'error');
        }
        if ($users->findByEmail($email)) {
            return $this->withFlash('/', 'This email address is already registered. Please login or wait for admin approval.', 'warning');
        }

        $now = date('c');
        $users->create([
            'id' => bin2hex(random_bytes(8)),
            'name' => $name,
            'email' => $email,
            'age' => (int)$age,
            'status' => 'pending',
            'created_at' => $now,
            'confirmed_at' => null,
            'disabled_at' => null,
        ]);

        return $this->withFlash('/', 'Registration successful. Please wait for admin approval before logging in.', 'success');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function withFlash(string $to, string $message, string $status)
    {
        return redirect()->to($to)->with('message', $message)->with('status', $status);
    }
}
