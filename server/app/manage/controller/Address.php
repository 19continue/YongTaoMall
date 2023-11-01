<?php

namespace app\manage\controller;

use app\manage\model\AddressModel;
use app\manage\model\CityModel;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Request;
use app\BaseController;

class Address extends BaseController
{
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $address_id=Request::post('address_id');
        $name=Request::post('name');
        $is_default=Request::post('is_default');
        $uid=Request::post('uid');
        $baseQuery = AddressModel::field('address_id,name,phone,area,detail,is_default,uid');
        if($address_id!=null){
            $baseQuery->where('address_id','=',$address_id);
        }
        if($name!=null){
            $baseQuery->where('name','like','%'.$name.'%');
        }
        if($is_default!=null){
            $baseQuery->where('is_default','=',$is_default);
        }
        if($uid!=null){
            $baseQuery->where('uid','=',$uid);
        }
        try {
            $count = $baseQuery->count('address_id');
            $baseQuery->order(['is_default desc','address_id desc']);
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
        $address_id=Request::post('address_id');
        $name=Request::post('name');
        $phone=Request::post('phone');
        $area=Request::post('area');
        $detail=Request::post('detail');
        $is_default=Request::post('is_default');
        $uid=Request::post('uid');
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