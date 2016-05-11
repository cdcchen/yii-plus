<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/3/28
 * Time: 下午7:42
 */

namespace yiiplus\web;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yiiplus\config\UserConfig;

/**
 * Class User
 * @package yiiplus\web
 */
class User extends \yii\web\User
{
    /**
     * @var string|array|UserConfig
     */
    public $userConfig = 'userConfig';

    public function init()
    {
        parent::init();

        if ($this->userConfig !== null) {
            $this->userConfig = Instance::ensure($this->userConfig, UserConfig::className());
        }
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getConfig($key, $defaultValue = null)
    {
        if ($this->userConfig instanceof UserConfig) {
            return $this->userConfig->get($this->id, $key, $defaultValue);
        } else {
            throw new InvalidConfigException('userConfig component is required');
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws InvalidConfigException
     */
    public function addConfig($key, $value)
    {
        if ($this->userConfig instanceof UserConfig) {
            return $this->userConfig->add($this->id, $key, $value);
        } else {
            throw new InvalidConfigException('userConfig component is required');
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     * @throws InvalidConfigException
     */
    public function setConfig($key, $value)
    {
        if ($this->userConfig instanceof UserConfig) {
            return $this->userConfig->set($this->id, $key, $value);
        } else {
            throw new InvalidConfigException('userConfig component is required');
        }
    }

    public function deleteConfig($key)
    {
        if ($this->userConfig instanceof UserConfig) {
            return $this->userConfig->delete($this->id, $key);
        } else {
            throw new InvalidConfigException('userConfig component is required');
        }
    }
}