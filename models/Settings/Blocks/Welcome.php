<?php


namespace app\models\Settings\Blocks;

use app\models\Settings\SettingsBlockInterface;

class Welcome extends BlockBase implements SettingsBlockInterface
{
    /**
     * Welcome constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            ['channelName', 'string'],
            [['messages', 'defaultRoles'], 'array'],
            ['personalMessage', 'bool', false],
        ];
    }
}