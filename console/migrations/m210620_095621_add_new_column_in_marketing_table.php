<?php

use yii\db\Migration;

/**
 * Class m210620_095621_add_new_column_in_marketing_table
 */
class m210620_095621_add_new_column_in_marketing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('marketing','img',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('marketing','img');
    }
}
