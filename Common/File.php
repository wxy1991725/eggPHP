<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of File
 *
 * @author Administrator
 */
class File {

//put your code here
    public static function filelist() {
        
    }

    public static function create_file($filename, $txt) {
        try {
            $fh = fopen($filename, 'w');
            fwrite($fh, $txt);
            fclose($fh);
            return true;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public static function read_file($filename) {
        try {
            $query = '';
            $fh = fopen($filename, 'r');
            while (!feof($fh)) {
                $line = rtrim(fgets($fp, 1024));
                $query.=$line;
            }
            fclose($fh);
            return $query;
        } catch (Exception $e) {
            echo $e;
        }
    }

}

?>
