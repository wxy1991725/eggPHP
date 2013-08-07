<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of home
 *
 * @author WXY
 */
class home_class extends Controller {

    //put your code here
    function index_action($id = null) {
        
        Tools::loadHelper('ip');
//        $area = $ip->getlocation('122.240.151.204');
//        $country=  iconv('GBK', 'UTF-8', $area['country']);
//        echo $country
    }

}

?>