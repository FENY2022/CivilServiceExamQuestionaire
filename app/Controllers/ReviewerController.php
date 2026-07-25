<?php

namespace App\Controllers;

use App\Libraries\Auth;
use App\Libraries\QuestionBank;

class ReviewerController extends BaseController
{
    public function index()
    {
        $redirect = (new Auth())->requireLogin();
        if ($redirect) {
            return $redirect;
        }

        $type = strtolower((string)($this->request->getGet('type') ?? 'professional'));
        if (!in_array($type, ['professional', 'subprofessional'], true)) {
            $type = 'professional';
        }

        $exam = (new QuestionBank())->getExamQuestions($type);
        $exam['timerEnabled'] = ($this->request->getGet('timer') ?? '1') !== '0';

        $extraHtml = '<div class="flex w-fit items-center gap-2 rounded-full border border-blue-100 bg-white/95 px-4 py-2 text-xs font-black text-slate-500 shadow-lg shadow-blue-900/10 lg:hidden"><span>Time Remaining</span><strong class="text-base text-brand-700" id="mobileTimer">--:--:--</strong></div>';

        return view('reviewer/index', [
            'title' => ucfirst($type) . ' Reviewer - ' . config('App')->appName,
            'active' => 'dashboard',
            'exam' => $exam,
            'type' => $type,
            'extraHtml' => $extraHtml,
        ]);
    }
}
