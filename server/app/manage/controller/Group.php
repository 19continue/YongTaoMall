<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\AuthorityModel;
use app\manage\model\GroupModel;
use app\Result;
use think\db\exception\DbException;
use think\facade\Db;
use think\facade\Request;
use think\response\Json;

class Group extends BaseController
{
    protected $model=null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new GroupModel();
    }
    public function getAll(): Json
    {
        $groups = $this->model::select();
        return Result::get(200,$groups,'查询成功');
    }

    public function getGroup(): Json
    {
        $groups = Db::table('group,admin')->where('group.create_people=admin.admin_id')->field('group.*,admin.nick_name')->select();
        return Result::get(200,$groups,'查询成功');
    }

    public function getMenuById(): Json
    {
        $group_id = Request::param('group_id');
        if($group_id==null){
            return Result::get(600,null,'菜单获取失败!');;
        }
        $rs=Db::table('menu')->where('menu_id', 'IN', function ($query) use ($group_id) {
            $query->table('authority')->where('group_id', $group_id )->field('menu_id');
        })->column('menu_id');
        return Result::get(200,$rs,'查询成功!');
    }

    public  function addGroup(): Json
    {
        $group_name = Request::post('group_name');
        $status = Request::post('status',1);
        $create_people = Request::post('create_people');
        $group = $this->model::where('group_name',$group_name)->find();
        if($group!=null){
            return Result::get(600,null,'该权限组已存在，请更换组名!');
        }
        $rs = $this->model::create([
            'group_name'  =>  $group_name,
            'status' => $status,
            'create_people' => $create_people
        ]);
        return Result::get(200,$rs,'创建成功!');
    }

    public function  deleteGroup(): Json
    {
        $group_id = Request::param('group_id');
        $this->model::where('group_id',$group_id)->delete();
        try {
            AuthorityModel::where('group_id', $group_id)->delete();
        } catch (DbException $e) {
            return Result::get(600,null,'删除失败!');
        }
        return Result::get(200,null,'删除成功!');
    }
    public function saveGroup(): Json
    {
        $group_id = Request::param('group_id');
        $group_name = Request::param('group_name');
        $status = Request::param('status');
        $create_people = Request::param('create_people');
        $group = $this->model::where('group_id',$group_id)->find();
        if($group!=null){
            $time=time();
            $create_time=date('Y-m-d H:i:s',$time);
            $group->group_name = $group_name;
            $group->status = $status;
            $group->create_people = $create_people;
            $group->create_time = $create_time;
            $group->save();
            try {
                AuthorityModel::where('group_id', $group_id)->delete();
            } catch (DbException $e) {
                return Result::get(600,$e,'保存失败!');
            }
        }
        else{
            return Result::get(600,null,'保存失败!');
        }
        return Result::get(200,$group,'保存成功!');
    }
}