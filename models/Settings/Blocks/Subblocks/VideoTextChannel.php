<?php


namespace app\models\Settings\Blocks\Subblocks;


use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class VideoTextChannel extends BlockBase implements SettingsBlockInterface
{
    /**
     * VideoTextChannel constructor.
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
            ['channelList', 'collection', 'VideoTextChannelItem'],
        ];
    }
}