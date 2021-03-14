<?php

if(!function_exists('prepare_directory')){
    /**
     * @param string $directory_to_create
     * @param string $path
     * @param int $user_id
     * @param int $firm_id
     * @return string
     */
    function prepare_directory($directory_to_create = '', $write = true) {
        $path = '';
        $directories = [];

        if(is_array($directory_to_create)){
            foreach($directory_to_create as $dtc){
                $directories[] = $dtc;
            }
        } else {
            $directories[] = $directory_to_create;
        }

        foreach($directories as $directory){
            if(empty($directory)){
                break;
            }
            if($write && !is_dir($directory)){
                mkdir($directory);
            }
            $path .= DIRECTORY_SEPARATOR . $directory;
        }

        $path .= DIRECTORY_SEPARATOR;

        return $path;
    }
}

if(!function_exists('untrailingslashit')){
    function untrailingslashit($path = ''){
        return rtrim($path, '/');
    }
}
