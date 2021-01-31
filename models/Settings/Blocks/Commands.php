<?php


namespace app\models\Settings\Blocks;


use app\models\Settings\SettingsBlockInterface;

class Commands extends BlockBase implements SettingsBlockInterface
{
    /**
     * Commands constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            ['processCommands', 'bool'],
            [['commandsRolesChannel', 'commandsRolesErrorMessage'], 'string'],
            ['commands', 'collection', 'Command'],
        ];
    }
}