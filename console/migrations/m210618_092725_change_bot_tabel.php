<?php

use yii\db\Migration;

/**
 * Class m210618_092725_change_bot_tabel
 */
class m210618_092725_change_bot_tabel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%bot}}', 'new_price', $this->decimal('15,5')->defaultValue(null));
        $this->addColumn('{{%bot}}', 'date_deadline', $this->string()->defaultValue(null));
        $this->addColumn('{{bot}}', 'created_at', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('{{bot}}', 'updated_at', $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'));
        $this->addColumn('{{bot}}', 'status_deadline', $this->smallInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%bot}}', 'new_price');
        $this->dropColumn('{{%bot}}', 'date_deadline');
        $this->dropColumn('{{%bot}}', 'created_at');
        $this->dropColumn('{{%bot}}', 'updated_at');
    }

}
