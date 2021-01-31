<?php


namespace app\models\Settings\Blocks\Subblocks;


use app\models\Settings\Blocks\BlockBase;
use app\models\Settings\SettingsBlockInterface;

class Command extends BlockBase implements SettingsBlockInterface
{
    /**
     * Command constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->init($settings);
    }

    public function fields()
    {
        return [
            [['type', 'hasMid', 'name', 'prefix', 'suffix', 'noArgSuffix', 'replaceArgFrom', 'replaceArgWith', 'channelName', 'content'], 'string'],
            [['hasOptionalSuffix', 'hasOptionalPrefix', 'hasNoArgSuffix', 'replaceArgContent'], 'bool'],
            ['options', 'collection', 'CommandOption'],
        ];
    }
    /*
    "options": {                                "options": [
        "м": {                                      {
            "suffix": "♂ Охотник",                      "name": "м",
            "prefix": ""                                "suffix": "♂ Охотник",
        },                              ==>             "prefix": ""
        "ж": {                                      },
            "suffix": "♀ Охотница",                 ...
            "prefix": ""
        }
    },                                           ]
    */
}