<?php

namespace App\Controllers;

use App\Libraries\Auth;

class QuizController extends BaseController
{
    public function submit()
    {
        $redirect = (new Auth())->requireLogin();
        if ($redirect) {
            return $redirect;
        }

        $payload = $this->request->getJSON(true) ?? [];
        $score = (int)($payload['score'] ?? 0);
        $total = max(1, (int)($payload['total'] ?? 1));
        $percent = round(($score / $total) * 100, 2);

        return $this->response->setJSON([
            'score' => $score,
            'total' => $total,
            'percent' => $percent,
            'passed' => $percent >= 80,
        ]);
    }
}
