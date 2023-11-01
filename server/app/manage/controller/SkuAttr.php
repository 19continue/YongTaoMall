<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\SkuAttrModel;

class SkuAttr extends BaseController
{
    protected $model=null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new SkuAttrModel();
    }

    public function insert(){
        $gid=Request::post('gid');
        $keys=Request::post('keys',[]);
        $attr=array();
        for($i=0;count($keys);$i++){
            $add=array();
            $add['gid']=$gid;
            $add['key']=$keys[$i];
            $attr[]=$add;
        }
        $rs=$this->model->saveAll($attr);
        return Result::get(200,$rs,'创建成功!');
    }
}