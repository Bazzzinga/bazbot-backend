<?php

namespace app\models\Settings\Blocks\Subblocks;

use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class StreamItem extends BlockBase implements SettingsBlockInterface
{
    /**
     * StreamItem constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            [['service', 'channel', 'name', 'thumbnail'], 'string'],
        ];
    }
}