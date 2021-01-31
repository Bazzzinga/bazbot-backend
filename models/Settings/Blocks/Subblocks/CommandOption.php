<?php


namespace app\models\Settings\Blocks\Subblocks;


use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class CommandOption extends BlockBase implements SettingsBlockInterface
{
    /**
     * Command constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            [['name', 'suffix', 'prefix'], 'string'],
        ];
    }
}