<?php

namespace App\Libraries;

class QuestionBank
{
    public function getExamQuestions(string $type): array
    {
        require_once ROOTPATH . 'questions' . DIRECTORY_SEPARATOR . 'question_bank.php';
        return get_exam_questions($type);
    }
}
