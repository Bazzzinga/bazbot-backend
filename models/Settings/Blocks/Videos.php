<?php


namespace app\models\Settings\Blocks;

use app\models\Settings\SettingsBlockInterface;

class Videos extends BlockBase implements SettingsBlockInterface
{
    /**
     * Videos constructor.
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
            ['textChannels', 'collection', 'VideoTextChannel'],
        ];
    }
}