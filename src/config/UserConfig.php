<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/3/28
 * Time: 下午6:28
 */

namespace yii\plus\config;


use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;

/**
 * Class UserConfig
 * @package yii\plus\config
 */
class UserConfig extends Component
{
    /**
     * @var Connection|array|string
     */
    public $db = 'db';

    /**
     * @var string
     */
    public $table = '{{%user_config}}';

    /**
     * @var Cache|array|string
     */
    public $cache;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (empty($this->db)) {
            throw new InvalidConfigException('UserConfig::db must be set.');
        }

        $this->db = Instance::ensure($this->db, Connection::className());

        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, Cache::className());
        }
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($userId, $key, $defaultValue = null)
    {
        static $values = [];
        $cacheKey = $this->buildCacheKey($userId, $key);

        if (!isset($values[$cacheKey])) {
            $value = $this->getFromCache($userId, $key);
            if ($value === false) {
                $value = $this->getFromDb($userId, $key);
            }

            $values[$cacheKey] = $value === false ? $defaultValue : $value;
        }

        return $values[$cacheKey];
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @param mixed $value
     * @return int
     * @throws \yii\db\Exception
     */
    public function set($userId, $key, $value)
    {
        if ($this->get($userId, $key) === null) {
            return $this->add($userId, $key, $value);
        } else {
            $condition = ['key' => $key, 'user_id' => $userId];
            $affectedRows = $this->db->createCommand()
                                     ->update($this->table, ['value' => $value], $condition)
                                     ->execute();

            if ($this->cacheIsActive() && $affectedRows) {
                $this->deleteFromCache($userId, $key);
            }

            return $affectedRows;
        }
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @param mixed $value
     * @return int
     * @throws \yii\db\Exception
     */
    public function add($userId, $key, $value)
    {
        $columns = [
            'user_id' => $userId,
            'key' => $key,
            'value' => $value
        ];

        return $this->db->createCommand()->insert($this->table, $columns)->execute();
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @return bool
     * @throws \yii\db\Exception
     */
    public function delete($userId, $key)
    {
        $transaction = $this->db->beginTransaction();

        $result = $this->deleteFromDb($userId, $key) && $this->deleteFromCache($userId, $key);
        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return $result;
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @return bool|mixed
     */
    protected function getFromCache($userId, $key)
    {
        if ($this->cacheIsActive()) {
            return $this->cache->get($this->buildCacheKey($userId, $key));
        } else {
            return false;
        }
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @return bool|string
     */
    protected function getFromDb($userId, $key)
    {
        $condition = ['user_id' => $userId, 'key' => $key];

        return (new Query())
            ->from([$this->table])
            ->select(['value'])
            ->where($condition)
            ->scalar($this->db);
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @return \yii\db\Command
     */
    protected function deleteFromDb($userId, $key)
    {
        $condition = ['user_id' => $userId, 'key' => $key];
        return $this->db->createCommand()->delete($this->table, $condition);
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @return bool
     */
    protected function deleteFromCache($userId, $key)
    {
        return $this->cache->delete($this->buildCacheKey($userId, $key));
    }

    /**
     * @return bool
     */
    protected function cacheIsActive()
    {
        return $this->cache instanceof Cache;
    }

    /**
     * @param int|string $userId
     * @param string $key
     * @return string
     */
    protected function buildCacheKey($userId, $key)
    {
        return $key . '_' . $userId;
    }
}