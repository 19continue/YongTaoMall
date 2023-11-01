<?php

namespace app\manage\controller;

use app\manage\model\AddressModel;
use app\manage\model\CityModel;
use app\manage\model\StoreModel;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Request;
use app\BaseController;

class Store extends BaseController
{
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $store_id=Request::post('store_id');
        $store_name=Request::post('store_name');
        $uid=Request::post('uid');
        $name=Request::post('name');
        $phone=Request::post('phone');
        $status=Request::post('status');
        $create_start=Request::post('create_start');
        $create_end=Request::post('create_end');
        $baseQuery = StoreModel::field('store_id,uid,store_name,avatar,page,money,income,sales,goods_score,service_score,logistics_score,name,phone,status,create_time');
        if($store_id!=null){
            $baseQuery->where('store_id','=',$store_id);
        }
        if($store_name!=null){
            $baseQuery->where('store_name','like','%'.$store_name.'%');
        }
        if($uid!=null){
            $baseQuery->where('uid','=',$uid);
        }
        if($name!=null){
            $baseQuery->where('name','like','%'.$name.'%');
        }
        if($phone!=null){
            $baseQuery->where('phone','like',$phone.'%');
        }
        if($status!=null){
            $baseQuery->where('$status','=',$status);
        }
        if($create_start!=null && $create_end!=null){
            $baseQuery->whereBetweenTime('create_time',$create_start,$create_end);
        }
        try {
            $count = $baseQuery->count('store_id');
            $baseQuery->order('store_id desc');
            if($pageSize!=null && $currentPage!=null){
                $pageSize=(int)$pageSize;
                $currentPage=(int)$currentPage;
                if($currentPage<1){
                    $currentPage=1;
                }
                $baseQuery->limit(($currentPage - 1) * $pageSize, $pageSize);
            }
            $data = $baseQuery->select();
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600,json_encode($e->getTrace()),'查询失败');
        }
        return Result::get(200,['records'=>$data,'total'=>$count],'查询成功');
    }


    /**
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function save(): \think\response\Json
    {
        $store_id=Request::post('store_id');
        $uid=Request::post('uid');
        $store_name=Request::post('store_name');
        $page=Request::post('page');
        $money=Request::post('page');
        $page=Request::post('page');
        $name=Request::post('name');
        $phone=Request::post('phone');
        $status=Request::post('status');
        $avatar=request()->file('avatar');
        $model = new AddressModel();
        $data=[];
        $address='';
        if($name!=null){
            $data['name']=$name;
        }
        if($phone!=null){
            $data['phone']=$phone;
        }
        if($area!=null){
            $area=json_decode($area);
            $area_name=CityModel::where('id','in',$area)->select()->toArray();
            foreach ($area_name as $k=>$v){
                $address=$address.$v['name'];
                if($k+1<count($area_name)){
                    $address=$address.'/';
                }
            }
            $data['area']=$address;
        }
        if($detail!=null){
            $data['detail']=$detail;
        }
        if($is_default!=null&&$uid!=null){
            $data['is_default']=$is_default;
            if($is_default==1){
                $one = AddressModel::where('is_default', 1)->where('uid', $uid)->find();
                if($one!=null){
                    $one->is_default=0;
                    $one->save();
                }
            }
        }
        if($uid!=null&&$address_id==null){
            $data['uid']=$uid;
        }
        if($address_id!=null){
            $update = $model::find($address_id);
            $save = $update->save($data);
            if($save){
                return Result::get(200,null,'修改成功');
            }
        }else{
            $save = $model->save($data);
            if($save){
                return Result::get(200,null,'添加成功');
            }
        }
        return Result::get(600,null,'操作失败');
    }
    public function delete(): \think\response\Json
    {
        $id=Request::post('id','[]');
        $id=json_decode($id);
        $destroy = AddressModel::destroy($id);
        if($destroy){
            return Result::get(200,null,'删除成功');
        }
        return Result::get(600,null,'删除失败');
    }
}