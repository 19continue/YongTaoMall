<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\AdminModel;
use app\manage\model\CityModel;
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

class City extends BaseController
{
    /**
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws DataNotFoundException
     */
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $id=Request::post('id');
        $name=Request::post('name');
        $parent=Request::post('parent',100000);
        $status=Request::post('status',1);
        $cityModel = new CityModel();
        $baseQuery = $cityModel::field('id,name,parent,depth,status');
        if($id!=null){
            $baseQuery->where('id','=',$id);
        }
        if($name!=null){
            $baseQuery->where('name','like','%'.$name.'%');
        }
        if($parent!=null){
            $baseQuery->where('parent','=',$parent);
        }
        if($status!=null){
            $baseQuery->where('status','=',$status);
        }
        $count = $baseQuery->count('id');
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

//    public function save(): \think\response\Json
//    {
//        $uid=Request::post('uid');
//        $login_name=Request::post('login_name');
//        $nick_name=Request::post('nick_name');
//        $password=Request::post('password');
//        $phone=Request::post('phone');
//        $email=Request::post('email');
//        $remark=Request::post('remark');
//        $money=Request::post('money');
//        $status=Request::post('status');
//        $level=Request::post('level');
//        $avatar=request()->file('avatar');
//        $model = new UserModel();
//        $user=[];
//        if($login_name!=null){
//            $user['login_name']=$login_name;
//        }
//        if($nick_name!=null){
//            $user['nick_name']=$nick_name;
//        }
//        if($password!=null){
//            $en_password=password_hash($password,PASSWORD_DEFAULT);
//            $user['password']=$en_password;
//        }
//        if($phone!=null){
//            $user['phone']=$phone;
//        }
//        if($email!=null){
//            $user['email']=$email;
//        }
//        if($remark!=null){
//            $user['remark']=$remark;
//        }
//        if($money!=null){
//            $user['money']=$money;
//        }
//        if($status!=null){
//            $user['status']=$status;
//        }
//        if($level!=null){
//            $user['level']=$level;
//        }
//        if($avatar!=null){
//            $save_name = \think\facade\Filesystem::disk('public')->putFile( 'categorize', $avatar);
//            $path='http://localhost/public/storage/'.$save_name;
//            $user['avatar']=$path;
//        }
//        if($uid!=null){
//            $update = $model::find($uid);
//            $save = $update->save($user);
//            if($save){
//                return Result::get(200,null,'保存成功');
//            }
//        }else{
//            $save = $model->save($user);
//            if($save){
//                return Result::get(200,null,'创建成功');
//            }
//        }
//        return Result::get(600,null,'操作失败');
//    }
//
//    public function delete(): \think\response\Json
//    {
//        $id=Request::post('id','[]');
//        $id=json_decode($id);
//        $destroy = UserModel::destroy($id);
//        if($destroy){
//            return Result::get(200,null,'删除成功');
//        }
//        return Result::get(600,null,'删除失败');
//    }

}