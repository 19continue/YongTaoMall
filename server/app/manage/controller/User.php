<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\AdminModel;
use app\manage\model\UserModel;
use app\manage\service\AdminService;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Request;
use think\response\Json;

//http://localhost/public/index.php/manage

class User extends BaseController
{

    public function login():Json
    {
        $login_name = Request::post('login_name');
        $password=Request::post('password');
        try {
            $user = UserModel::where('login_name', $login_name)->find();
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600,null,'无法查找该用户!');
        }
        if($user==null){
            return Result::get(600,null,'用户名不存在!');
        }
        if($user['status']==0){
            return Result::get(600,null,'用户已被冻结!');
        }
        if(password_verify($password,$user['password'])){
            $time=time();
            $login_time=date('Y-m-d H:i:s',$time);
            $user->login_time = $login_time;
            $user->save();
            $user->password='';
            return Result::get(200,$user,'登陆成功!');
        }
        else{
            return Result::get(600,null,'密码错误!');
        }
    }


    public  function register(): Json
    {
        $phone = Request::post('phone');
        $login_name = Request::post('login_name');
        $password = Request::post('password');
        try {
            $user = UserModel::where('login_name', $login_name)->find();
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600,json_encode($e->getTrace()),'用户名已存在!');
        }
        if($user!=null){
            return Result::get(600,null,'用户名已存在!');
        }
        $en_password=password_hash($password,PASSWORD_DEFAULT);
        $nick_name='yt'.(time()%100000);
        $rs = UserModel::create([
            'login_name'  =>  $login_name,
            'nick_name'  =>  $nick_name,
            'password' =>  $en_password,
            'phone' => $phone
        ]);
        return Result::get(200,$rs,'注册成功!');
    }

    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $uid=Request::post('uid');
        $login_name=Request::post('login_name');
        $nick_name=Request::post('nick_name');
        $phone=Request::post('phone');
        $status=Request::post('status');
        $level=Request::post('level');
        $login_start=Request::post('login_start');
        $login_end=Request::post('login_end');
        $create_start=Request::post('create_start');
        $create_end=Request::post('create_end');
        $userModel = new UserModel();
        $baseQuery = $userModel::field('uid,login_name,nick_name,avatar,phone,email,remark,money,status,create_time,login_time,level,type');
        if($uid!=null){
            $baseQuery->where('uid','=',$uid);
        }
        if($login_name!=null){
            $baseQuery->where('login_name','like',$login_name.'%');
        }
        if($nick_name!=null){
            $baseQuery->where('nick_name','like','%'.$nick_name.'%');
        }
        if($phone!=null){
            $baseQuery->where('phone','like',$phone.'%');
        }
        if($status!=null){
            $baseQuery->where('status','=',$status);
        }
        if($level!=null){
            $baseQuery->where('level','>=',$level);
        }
        if($login_start!=null && $login_end!=null){
            $baseQuery->whereBetweenTime('login_time',$login_start,$login_end);
        }
        if($create_start!=null && $create_end!=null){
            $baseQuery->whereBetweenTime('create_time',$create_start,$create_end);
        }
        $count = $baseQuery->count('uid');
        $baseQuery->order('create_time desc');
        if($pageSize!=null && $currentPage!=null){
            $pageSize=(int)$pageSize;
            $currentPage=(int)$currentPage;
            if($currentPage<1){
                $currentPage=1;
            }
            $baseQuery->limit(($currentPage - 1) * $pageSize, $pageSize);
        }
        $data = $baseQuery->select();
        return Result::get(200,['records'=>$data,'total'=>$count],'查询成功');
    }

    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
    public function save(): \think\response\Json
    {
        $uid=Request::post('uid');
        $login_name=Request::post('login_name');
        $nick_name=Request::post('nick_name');
        $password=Request::post('password');
        $phone=Request::post('phone');
        $email=Request::post('email');
        $remark=Request::post('remark');
        $money=Request::post('money');
        $status=Request::post('status');
        $level=Request::post('level');
        $avatar=request()->file('avatar');
        $model = new UserModel();
        $user=[];
        if($login_name!=null){
            $user['login_name']=$login_name;
            $count = UserModel::where('login_name', $login_name)->count('uid');
            if($count>0){
                return Result::get(600,null,'该用户名已存在');
            }
        }
        if($nick_name!=null){
            $user['nick_name']=$nick_name;
        }
        if($password!=null){
            $en_password=password_hash($password,PASSWORD_DEFAULT);
            $user['password']=$en_password;
        }
        if($phone!=null){
            $user['phone']=$phone;
        }
        if($email!=null){
            $user['email']=$email;
        }
        if($remark!=null){
            $user['remark']=$remark;
        }
        if($money!=null){
            $user['money']=$money;
        }
        if($status!=null){
            $user['status']=$status;
        }
        if($level!=null){
            $user['level']=$level;
        }
        if($avatar!=null){
            $save_name = \think\facade\Filesystem::disk('public')->putFile( 'categorize', $avatar);
            $path='http://localhost/public/storage/'.$save_name;
            $user['avatar']=$path;
        }
        if($uid!=null){
            $update = $model::find($uid);
            $save = $update->save($user);
            if($save){
                return Result::get(200,null,'保存成功');
            }
        }else{
            $save = $model->save($user);
            if($save){
                return Result::get(200,null,'创建成功');
            }
        }
        return Result::get(600,null,'操作失败');
    }

    public function delete(): \think\response\Json
    {
        $id=Request::post('id','[]');
        $id=json_decode($id);
        $destroy = UserModel::destroy($id);
        if($destroy){
            return Result::get(200,null,'删除成功');
        }
        return Result::get(600,null,'删除失败');
    }
    public function reset(): \think\response\Json
    {
        $uid=Request::post('uid');
        $update = UserModel::find($uid);
        $password=password_hash('123456',PASSWORD_DEFAULT);
        $save = $update->save(['password'=>$password]);
        if($save){
            return Result::get(200,null,'密码重置成功');
        }
        return Result::get(600,null,'密码重置失败');
    }
}