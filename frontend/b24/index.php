<?php

    require_once('classes/request_class.php');
    require_once('classes/crest.php');
    
    $deal_find = CRest :: call('crm.deal.get', ['ID' => '860']);
    
    print_r('<pre>'); print_r($deal_find); print_r('</pre>');


?>