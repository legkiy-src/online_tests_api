<?php

declare(strict_types=1);

namespace App\Enum\Question;

enum QuestionType: string
{
    case SINGLE_CHOICE = 'single_choice';
    case MULTIPLE_CHOICE = 'multiple_choice';
    case TEXT = 'text';
    case TRUE_FALSE = 'true_false';
    case MATCHING = 'matching';
    case SEQUENCE = 'sequence';

    public function label(): string
    {
        return match($this) {
            self::SINGLE_CHOICE => 'Один вариант ответа',
            self::MULTIPLE_CHOICE => 'Несколько вариантов ответа',
            self::TEXT => 'Текстовый ответ',
            self::TRUE_FALSE => 'Верно/Неверно',
            self::MATCHING => 'Сопоставление',
            self::SEQUENCE => 'Последовательность',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::SINGLE_CHOICE => 'Выбор одного правильного варианта из нескольких',
            self::MULTIPLE_CHOICE => 'Выбор нескольких правильных вариантов',
            self::TEXT => 'Свободный текстовый ответ',
            self::TRUE_FALSE => 'Определение верности утверждения',
            self::MATCHING => 'Сопоставление элементов из двух списков',
            self::SEQUENCE => 'Расстановка элементов в правильном порядке',
        };
    }

    public function hasOptions(): bool
    {
        return in_array($this, [
            self::SINGLE_CHOICE,
            self::MULTIPLE_CHOICE,
            self::TRUE_FALSE,
            self::MATCHING,
            self::SEQUENCE,
        ]);
    }

    public function isAutoGradable(): bool
    {
        return $this !== self::TEXT;
    }

    public function allowsMultipleAnswers(): bool
    {
        return $this === self::MULTIPLE_CHOICE;
    }

    public function requiresManualGrading(): bool
    {
        return $this === self::TEXT;
    }

    public function getDefaultPoints(): int
    {
        return match($this) {
            self::SINGLE_CHOICE => 1,
            self::MULTIPLE_CHOICE => 2,
            self::TEXT => 3,
            self::TRUE_FALSE => 1,
            self::MATCHING => 2,
            self::SEQUENCE => 2,
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::SINGLE_CHOICE => '🔘',
            self::MULTIPLE_CHOICE => '☑️',
            self::TEXT => '📝',
            self::TRUE_FALSE => '❌✅',
            self::MATCHING => '🔄',
            self::SEQUENCE => '🔢',
        };
    }

    public static function choices(): array
    {
        return [
            self::SINGLE_CHOICE->label() => self::SINGLE_CHOICE->value,
            self::MULTIPLE_CHOICE->label() => self::MULTIPLE_CHOICE->value,
            self::TEXT->label() => self::TEXT->value,
            self::TRUE_FALSE->label() => self::TRUE_FALSE->value,
            self::MATCHING->label() => self::MATCHING->value,
            self::SEQUENCE->label() => self::SEQUENCE->value,
        ];
    }

    public static function autoGradableTypes(): array
    {
        return array_filter(self::cases(), fn($type) => $type->isAutoGradable());
    }

    public static function manualGradingTypes(): array
    {
        return array_filter(self::cases(), fn($type) => $type->requiresManualGrading());
    }

    public function getValidationRules(): array
    {
        return match($this) {
            self::SINGLE_CHOICE => [
                'min_answers' => 2,
                'max_answers' => 10,
                'require_correct' => true,
                'single_correct' => true,
            ],
            self::MULTIPLE_CHOICE => [
                'min_answers' => 2,
                'max_answers' => 10,
                'require_correct' => true,
                'single_correct' => false,
            ],
            self::TEXT => [
                'min_length' => 1,
                'max_length' => 5000,
                'allow_attachments' => true,
            ],
            self::TRUE_FALSE => [
                'min_answers' => 2,
                'max_answers' => 2,
                'require_correct' => true,
            ],
            self::MATCHING => [
                'min_pairs' => 2,
                'max_pairs' => 8,
            ],
            self::SEQUENCE => [
                'min_items' => 2,
                'max_items' => 10,
            ],
        };
    }
}
