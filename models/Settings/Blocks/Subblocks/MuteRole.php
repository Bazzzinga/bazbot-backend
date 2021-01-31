<?php

namespace app\models\Settings\Blocks\Subblocks;

use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class MuteRole extends BlockBase implements SettingsBlockInterface
{
    /**
     * MuteRole constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            ['role', 'string', ''],
            ['do', 'string', '+'],
        ];
    }
}