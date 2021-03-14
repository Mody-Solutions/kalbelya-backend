<?php
$path = dirname(__FILE__);
foreach(glob("{$path}/*_helper.php") as $file){
    require_once $file;
}
