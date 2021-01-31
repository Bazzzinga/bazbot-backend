<?php


namespace app\models\Settings\Blocks;

use app\models\Settings\SettingsBlockInterface;

class Streams extends BlockBase implements SettingsBlockInterface
{
    /**
     * Streams constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            ['interval', 'integer'],
            [['channelName', 'message', 'streamTitlePrefix', 'game'], 'string'],
            ['streamList', 'collection', 'StreamItem'],
        ];
    }
}