<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%marketing}}`.
 */
class m210619_074415_create_marketing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%marketing}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'file' => $this->string(255),
            'status' => $this->smallInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%marketing}}');
    }
}
