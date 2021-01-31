<?php

namespace app\models\Settings\Blocks\Subblocks;

use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class MuteLevel extends BlockBase implements SettingsBlockInterface
{
    /**
     * MuteLevel constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            [['seconds', 'label'], 'string', ''],
        ];
    }
}
