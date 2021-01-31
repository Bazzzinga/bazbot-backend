<?php


namespace app\models\Settings\Blocks;

use app\models\Settings\SettingsBlockInterface;

class Online extends BlockBase implements SettingsBlockInterface
{
    /**
     * Online constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
       $this->init($settings);
    }

    public function fields()
    {
        return [
            ['interval', 'integer', 0],
            ['textChannels', 'collection', 'OnlineChannel'],
        ];
    }
}