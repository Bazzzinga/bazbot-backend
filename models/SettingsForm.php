<?php


namespace app\models;

use yii\base\Model;
use app\models\Users;
use app\models\Settings\SettingsBase;

class SettingsForm extends Model
{
    public $settings;

    private $settingsBase;

    public function rules()
    {
        return [
            [['settings'], 'string'],
        ];
    }


}
