<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 14-9-8
 * Time: 下午1:23
 */

namespace yii\plus\i18n;


/**
 * Class Formatter
 * @package yii\plus\i18n
 */
class Formatter extends \yii\i18n\Formatter
{
    /**
     * @param string|integer|float $value value in bytes to be formatted.
     * @param integer $decimals the number of digits after the decimal point.
     * @param bool $uppercase
     * @return float|int|string
     */
    public function asSizeNumber($value, $decimals = 0, $uppercase = false)
    {
        $position = 0;

        do {
            if ($value < 1024) {
                break;
            }

            $value = $value / 1024;
            $position++;
        } while ($position < 5);

        $value = round($value, $decimals);
        switch ($position) {
            case 0:
                return $value;
            case 1:
                $value .= $uppercase ? 'K' : 'k';
                break;
            case 2:
                $value .= $uppercase ? 'M' : 'm';
                break;
            case 3:
                $value .= $uppercase ? 'G' : 'g';
                break;
            case 4:
                $value .= $uppercase ? 'T' : 't';
                break;
            default:
                $value .= $uppercase ? 'P' : 'p';
                break;
        }

        return $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public function asPlain($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $value = strip_tags($value);

        return $value;
    }
} 