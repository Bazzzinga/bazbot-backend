<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%token}}`.
 */
class m210303_200555_create_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%token}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->unique()->notNull()->comment('Внутреннее название сервиса'),
            'token' => $this->string()->notNull()->comment('Токен'),
            'updated_at' => $this->dateTime()->comment('Дата обновления'),
            'life_time' => $this->integer()->comment('Время жизни токена')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%token}}');
    }
}
