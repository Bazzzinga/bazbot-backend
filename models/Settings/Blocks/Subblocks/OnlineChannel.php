<?php

namespace app\models\Settings\Blocks\Subblocks;

use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class OnlineChannel extends BlockBase implements SettingsBlockInterface
{
    /**
     * OnlineChannel constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            [['channelName', 'steamGameID', 'gameName', 'message', 'thumbnail'], 'string'],
        ];
    }
}
