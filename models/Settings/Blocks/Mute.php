<?php


namespace app\models\Settings\Blocks;

use app\models\Settings\SettingsBlockInterface;

class Mute extends BlockBase implements SettingsBlockInterface
{
    /**
     * Mute constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            [['moderatorMessage', 'warning', 'messages'], 'string'],
            ['moderators', 'array'],
            ['roles', 'collection', 'MuteRole'],
            ['muteLevels', 'collection', 'MuteLevel'],
        ];
    }
}