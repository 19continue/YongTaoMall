<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\AdminModel;
use app\manage\service\AdminService;
use app\Result;
use think\facade\Db;
use think\facade\Request;
use think\response\Json;

//http://localhost/public/index.php/manage

class Admin extends BaseController
{
    protected $model=null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new AdminModel();
    }

    public function login():Json
    {
        $name = Request::post('name','');
        $password=Request::post('password','');
        $admin = $this->model::where('login_name',$name)->find();
        if($admin==null){
            return Result::get(600,null,'用户名不存在!');
        }
        if($admin['status']==0){
            return Result::get(600,null,'用户已被冻结!');
        }
        if(password_verify($password,$admin['password'])){
            $time=time();
            $login_time=date('Y-m-d H:i:s',$time);
            $admin->login_time = $login_time;
            $admin->save();
            return Result::get(200,$admin,'登陆成功!');
        }
        else{
            return Result::get(600,null,'密码错误!');
        }
    }

    /**
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\DataNotFoundException
     */
    public function getMenu() : Json
    {
        $group_id = Request::param('group_id');
        if($group_id==null){
            return Result::get(600,null,'权限获取失败!');;
        }
        $level_count=Db::table('menu')
            ->where('menu_id', 'IN', function ($query) use ($group_id) {
                $query->table('authority')->where('group_id', $group_id )->field('menu_id');
            })->group('level')->count('level');
        $menu=array();
        for ($i=0;$i<$level_count;$i++){
            $rs=Db::table('menu')->where('level',$i)->where('menu_id', 'IN', function ($query) use ($group_id) {
                $query->table('authority')->where('group_id', $group_id )->field('menu_id');
            })->select();
            $menu[] = $rs;
        }
        return Result::get(200,$menu,'查询成功!');
    }

    public function getAll(): Json
    {
        $admins = Db::table('admin,group')->where('admin.group_id=group.group_id')->field('admin.*,group.group_name')->select();
        return Result::get(200,$admins,'查询成功!');
    }

    public function selectById(): Json
    {
        $admin_id = Request::param('admin_id');
        return Result::get(200,$this->model::where('admin_id',$admin_id)->find(),'查询成功!');
    }

    public function buttonStatus(): Json
    {
        $group_id = Request::post('group_id');
        $menu_id = Request::post('menu_id');
        $button = Db::table('authority')->where(['group_id' => $group_id, 'menu_id' => $menu_id])->field('button')->find();
        return Result::get(200,$button,'查询成功!');
    }

    public  function addAdmin(): Json
    {
        $login_name = Request::post('login_name','');
        $password = Request::post('password',123456);
        $nick_name = Request::post('nick_name','');
        $status = Request::post('status',1);
        $group_id = Request::post('group_id',2);
        $admin = $this->model::where('login_name',$login_name)->find();
        if($admin!=null){
            return Result::get(600,null,'用户名已存在!');
        }
        $en_password=password_hash($password,PASSWORD_DEFAULT);
        $rs = $this->model::create([
            'login_name'  =>  $login_name,
            'password' =>  $en_password,
            'nick_name' => $nick_name,
            'status' => $status,
            'group_id' => $group_id
        ]);
        return Result::get(200,$rs,'创建成功!');
    }

    public function uploadAvatar(): Json
    {
        $admin_id=Request::param('admin_id',0);
        $file = request()->file('avatar');
        if($file==null){
            return Result::get(600,null,'头像上传失败!');
        }
        $save_name = \think\facade\Filesystem::disk('public')->putFile( 'admin', $file);
        $path='http://localhost/public/storage/'.$save_name;
        $admin = $this->model::where('admin_id',$admin_id)->find();
        if($admin==null){
            return Result::get(600,null,'头像上传失败!');
        }
        $admin->avatar = $path;
        $admin->save();
        return Result::get(200,$path,'上传成功!');
    }

    public function deleteById(): Json
    {
        $admin_id=Request::post('admin_id',0);
        $rs = $this->model::find($admin_id);
        $rs->delete();
        return Result::get(200,null,'删除成功!');
    }
    public  function updateAdmin(): Json
    {
        $admin_id = Request::post('admin_id');
        $login_name = Request::post('login_name','');
        $password = Request::post('password','');
        $nick_name = Request::post('nick_name');
        $status = Request::post('status');
        $group_id = Request::post('group_id');
        $admin = $this->model::where('admin_id',$admin_id)->find();
        $is_have = $this->model::where('login_name',$login_name)->select();
        if($admin==null){
            return Result::get(600,null,'保存失败!');
        }
        if(count($is_have)>1){
            return Result::get(600,null,'用户名已存在!');
        }
        $admin -> login_name = $login_name;
        $admin -> nick_name  = $nick_name;
        $admin -> login_name = $login_name;
        $admin -> status = $status;
        $admin -> group_id = $group_id;
        if($password!='') {
            $en_password = password_hash($password, PASSWORD_DEFAULT);
            $admin -> password = $en_password;
        }
        $admin -> save();
        return Result::get(200,$admin,'保存成功!');
    }
}