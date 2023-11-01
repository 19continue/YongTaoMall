<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\AuthorityModel;
use app\Result;
use think\facade\Request;
use \think\response\Json;

class Authority extends BaseController
{
    protected $model=null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new AuthorityModel();
    }

    public function getByGroupId(){
        $group_id=Request::post('group_id');
        $rs=$this->model::where('group_id',$group_id)->select();
        return Result::get(200,$rs,'查询成功!');
    }

    public function saveAll(): Json
    {
        $group_id=Request::post('group_id');
        $list=explode(',',Request::post('list'));
        $authority=array();
        foreach ($list as  $value){
            $arr=explode('.',$value);
            $add=array();
            $add['group_id']=$group_id;
            $add['menu_id']=$arr[0];
            if(array_key_exists(1,$arr)){
                $add['button']=$arr[1];
            }
            else{
                $add['button']=0;
            }
            $authority[]=$add;
        }
        $this->model->saveAll($authority);
        return Result::get(200,$authority,'创建成功!');
    }

}