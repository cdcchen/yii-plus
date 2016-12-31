<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2016/12/31
 * Time: 15:15
 */

namespace yiiplus\i18n;


use Yii;
use yii\base\Object;


/**
 * Class DateTimeFormatter
 * @package common\models
 */
class DateTimeFormatter extends Object
{
    /**
     * @var \DateTime|int|string
     */
    protected $value;

    /**
     * DateTimeFormatter constructor.
     * @param integer|string|\DateTime $value
     * @param array $config
     */
    public function __construct($value, $config = [])
    {
        $this->value = $value;
        parent::__construct($config);
    }

    /**
     * @param null $format
     * @return string
     */
    public function asDateTime($format = null)
    {
        return Yii::$app->getFormatter()->asDatetime($this->value, $format);
    }

    /**
     * @param null $format
     * @return string
     */
    public function asDate($format = null)
    {
        return Yii::$app->getFormatter()->asDate($this->value, $format);
    }

    /**
     * @param null $format
     * @return string
     */
    public function asTime($format = null)
    {
        return Yii::$app->getFormatter()->asTime($this->value, $format);
    }

    /**
     * @return string
     */
    public function asTimestamp()
    {
        return Yii::$app->getFormatter()->asTimestamp($this->value);
    }

    /**
     * @param null $referenceTime
     * @return string
     */
    public function asRelativeTime($referenceTime = null)
    {
        return Yii::$app->getFormatter()->asRelativeTime($this->value, $referenceTime);
    }

    /**
     * @param string $implodeString
     * @param string $negativeSign
     * @return string
     */
    public function asDuration($implodeString = ', ', $negativeSign = '-')
    {
        return Yii::$app->getFormatter()->asDuration($this->value, $implodeString, $negativeSign);
    }
}