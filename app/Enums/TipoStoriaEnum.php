<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TipoStoriaEnum extends Enum
{
    const IN_CORSO = 'in_corso';
    const FINITA = 'finita';

    public static function getDescription($value): string
    {
        switch ($value) {
            case self::IN_CORSO:
                return 'In Corso';
                break;
            case self::FINITA:
                return 'Finita';
                break;
            default:
                return self::getKey($value);
        }
    }
}