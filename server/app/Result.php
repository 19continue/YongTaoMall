<?php

namespace app;

class Result
{
    public static $baseURL='http://localhost/';
    public static function get($code,$data,$msg){
       return json([
           'code'=>$code,
           'data'=>$data,
           'msg'=>$msg
       ]);
    }
}