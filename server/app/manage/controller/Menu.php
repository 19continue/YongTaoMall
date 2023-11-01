<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\MenuModel;
use app\Result;
use think\facade\Db;
use think\facade\Request;
use think\response\Json;

class Menu extends BaseController
{
    protected $model=null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new MenuModel();
    }

    public function getAll(){
        $menu = $this->model::select();
        return Result::get(200,$menu,'查询成功');
    }

    public function levelGetAll() : Json{
            $level_count=$this->model->group('level')->count('level');
            $menu=array();
            for ($i=0;$i<$level_count;$i++){
                $rs=$this->model->where('level',$i)->select();
                $menu[] = $rs;
            }
            return Result::get(200,$menu,'查询成功!');
    }

}