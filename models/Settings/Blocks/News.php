<?php


namespace app\models\Settings\Blocks;


use app\models\Settings\SettingsBlockInterface;

class News extends BlockBase implements SettingsBlockInterface
{
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [];
    }
}