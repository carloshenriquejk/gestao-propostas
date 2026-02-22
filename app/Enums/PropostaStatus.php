<?php

namespace App\Enums;

enum PropostaStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case CANCELED = 'CANCELED';

    /**
     * Estados finais (imutáveis)
     */
    public function isFinal(): bool
    {
        return in_array($this, [
            self::APPROVED,
            self::REJECTED,
            self::CANCELED,
        ]);
    }

    /**
     * Define transições válidas
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::DRAFT => in_array($newStatus, [
                self::SUBMITTED,
                self::CANCELED
            ]),

            self::SUBMITTED => in_array($newStatus, [
                self::APPROVED,
                self::REJECTED,
                self::CANCELED
            ]),

            self::APPROVED,
            self::REJECTED,
            self::CANCELED => false,
        };
    }

    /**
     * Retorna lista de transições possíveis
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::DRAFT => [
                self::SUBMITTED,
                self::CANCELED
            ],

            self::SUBMITTED => [
                self::APPROVED,
                self::REJECTED,
                self::CANCELED
            ],

            default => [],
        };
    }
}