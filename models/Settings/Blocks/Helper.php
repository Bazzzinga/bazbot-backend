<?php


namespace app\models\Settings\Blocks;

use yii\helpers\ArrayHelper;

class Helper
{
    public static function simpleField($settings, $key, $default)
    {
        if (ArrayHelper::keyExists($key, $settings)) {
            return $settings[$key];
        } else {
            return $default;
        }
    }

    public static function classCollectionField($settings, $key, $className)
    {
        $result = [];
        $class = "Subblocks\\" . $className;
        if (ArrayHelper::keyExists($key, $settings)) {
            foreach ($settings[$key] as $info) {
                $result[] = new $class($info);
            }
        }
        return $result;
    }
}
