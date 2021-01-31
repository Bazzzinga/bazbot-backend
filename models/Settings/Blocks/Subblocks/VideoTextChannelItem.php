<?php


namespace app\models\Settings\Blocks\Subblocks;


use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class VideoTextChannelItem extends BlockBase implements SettingsBlockInterface
{
    /**
     * VideoTextChannelItem constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            [['name', 'channel', 'message'], 'string'],
        ];
    }
}