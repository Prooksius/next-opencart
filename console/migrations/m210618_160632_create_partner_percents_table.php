<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner_percents}}`.
 */
class m210618_160632_create_partner_percents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_percents}}', [
            'id' => $this->primaryKey(),
            'percent' => $this->decimal('10,2')->notNull(),
            'percent_text' => $this->text()->defaultValue(null),
            'status' => $this->smallInteger()->defaultValue(1),
            'sort' => $this->integer()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner_percents}}');
    }
}
