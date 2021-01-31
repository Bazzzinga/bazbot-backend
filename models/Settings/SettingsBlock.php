<?php


namespace app\models\Settings;


use yii\helpers\ArrayHelper;

class SettingsBlock extends SettingsBase
{
    /**
     * @var app\models\Settings\SettingsBlockInterface
     */
    public $block;

    /**
     * @var bool
     */
    private $isValid;

    private static $blocksNamespace = "\\app\\models\\Settings\\Blocks\\";

    /**
     * SettingsBlock constructor.
     * @param $block
     * @param $blockSettings
     */
    public function __construct($block, $blockSettings)
    {
        $this->isValid = false;
        $this->block = null;

        if (in_array($block, static::$blockList)) {
            try {
                $className = static::$blocksNamespace . $block;
                $this->block = new $className($blockSettings);
                $this->isValid = true;
            } catch (\Exception $e) {
                //not valid
            }
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }
}