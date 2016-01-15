<?php
namespace yiiplus\helpers;

use Yii;
use yii\web\UrlManager;

class BaseUrl extends \yii\helpers\BaseUrl
{
    public static function toRoute($route, $scheme = false, $urlManager = null)
    {
        $route = (array) $route;
        $route[0] = static::normalizeRoute($route[0]);

        if (!($urlManager instanceof UrlManager))
            $urlManager = Yii::$app->getUrlManager();

        if ($scheme) {
            return $urlManager->createAbsoluteUrl($route, is_string($scheme) ? $scheme : null);
        }
        else {
            return $urlManager->createUrl($route);
        }
    }

    public static function to($url = '', $scheme = false, $urlManager = null)
    {
        if (is_array($url)) {
            return static::toRoute($url, $scheme, $urlManager);
        }

        $url = Yii::getAlias($url);
        if ($url === '') {
            $url = Yii::$app->getRequest()->getUrl();
        }

        if (!$scheme) {
            return $url;
        }

        if (strncmp($url, '//', 2) === 0) {
            // e.g. //hostname/path/to/resource
            return is_string($scheme) ? "$scheme:$url" : $url;
        }

        if (($pos = strpos($url, ':')) == false || !ctype_alpha(substr($url, 0, $pos))) {
            // turn relative URL into absolute
            $url = Yii::$app->getUrlManager()->getHostInfo() . '/' . ltrim($url, '/');
        }

        if (is_string($scheme) && ($pos = strpos($url, ':')) !== false) {
            // replace the scheme with the specified one
            $url = $scheme . substr($url, $pos);
        }

        return $url;
    }
}