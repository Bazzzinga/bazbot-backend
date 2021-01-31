<?php


namespace app\models\Settings;


use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Settings extends SettingsBase
{
    private $settings;

    private $blocks;

    public function __construct()
    {
        $user = Yii::$app->user;

        $settingsString = $user->identity->settings;

        $this->settings = Json::decode($settingsString);
        if ($this->settings == null) {
            $this->settings = [];
        }

        $this->blocks = [];

        foreach (static::$blockList as $block) {
            $settings = [];
            if (ArrayHelper::keyExists($block, $this->settings)) {
                $settings = $this->settings;
            }
            $nextBlock = new SettingsBlock($block, $settings);
            if ($nextBlock->isValid()) {
               $this->blocks[] = $nextBlock;
            }
        }
    }
}