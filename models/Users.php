<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int $domains_id Платформа
 * @property string $login Логин
 * @property string $pass Пароль
 * @property string $created Дата создания
 * @property string $updated Дата изменения
 * @property int $active Активен
 * @property string $settings Настройки
 * @property string $auth_key
 * @property int $level
 */
class Users extends ActiveRecord implements IdentityInterface
{
    const LEVEL_USER = 0;
    const LEVEL_ADMIN = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => '\yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['domains_id', 'login', 'pass'], 'required'],
            [['domains_id', 'active', 'level', 'active'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['settings'], 'string'],
            [['login', 'pass'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domains_id' => 'Платформа',
            'login' => 'Логин',
            'pass' => 'Пароль',
            'created' => 'Дата создания',
            'updated' => 'Дата изменения',
            'active' => 'Активен',
            'settings' => 'Настройки',
            'level' => 'Права',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->pass);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->pass = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Set new user password
     * @param $password
     * @return $this
     */
    public function changePassword($password) {
        $this->setPassword($password);
        $this->generateAuthKey();
        $this->save();

        return $this;
    }

    /**
     * Check if user is admin
     * @return bool
     */
    public function isAdmin() {
        return $this->level == self::LEVEL_ADMIN;
    }


}
