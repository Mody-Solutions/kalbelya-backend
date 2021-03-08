<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function send_response($status_code, $msg = [], $XSSClean = false, $exclude_leading_zeros = []) {
        $global_fields_to_exclude_zeros = [];

        $exclude_leading_zeros = array_merge($exclude_leading_zeros, $global_fields_to_exclude_zeros);

        if(gettype($msg) == 'array' || gettype($msg) == 'object'){
            $this->_prepare_data($msg, $XSSClean, $exclude_leading_zeros);
        }

        return response()->json($msg, $status_code);
    }

    private function _prepare_data(&$msg, $XSSClean, $exclude_leading_zeros) {
        foreach($msg as $key => &$row){
            if(is_array($row) || is_object($row)){ // if this is an array of arrays or objects (collection)
                $this->_prepare_data($row, $XSSClean, $exclude_leading_zeros);
            } else { // if this is a single row
                if(!is_null($row)){
                    if($XSSClean){
                        $row = htmlentities($row);
                    }
                    if(is_numeric($row)){
                        if(!in_array($key, $exclude_leading_zeros)){
                            $row = $row + 0;
                        }
                    }
                }
            }
        }
    }
}
