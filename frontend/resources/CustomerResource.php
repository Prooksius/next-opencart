<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 08.09.2021
 * Time: 16:04
 */

namespace frontend\resources;


use common\models\Customer;

class CustomerResource extends Customer
{
    public function fields()
    {
        $fields = parent::fields();

        unset($fields['auth_key']);
        unset($fields['password_hash']);
        unset($fields['password_reset_token']);

        return $fields;
    }

}