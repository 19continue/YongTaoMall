<?php

namespace app\manage\controller;

use app\manage\model\ShoppingCartModel;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Request;
use app\BaseController;

class ShoppingCart extends BaseController
{
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $uid=Request::post('uid');
        $baseQuery =Db::table('shopping_cart')
            ->leftJoin('sku','shopping_cart.sku_id=sku.sku_id')
            ->leftJoin('goods_img','shopping_cart.gid=goods_img.gid and goods_img.first=1')
            ->leftJoin('goods','shopping_cart.gid=goods.gid')
            ->field('shopping_cart.*,sku.price,sku.picture,sku.amount,goods_img.src,goods.goods_name,goods.status,goods.delete_status');
        if($uid!=null){
            $baseQuery->where('uid','=',$uid);
        }
        try {
            $count = $baseQuery->count('shopping_cart.sid');
            $baseQuery->order('shopping_cart.sid desc');
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


    public function save(): \think\response\Json
    {
        $sid=Request::post('sid');
        $uid=Request::post('uid');
        $gid=Request::post('gid');
        $sku_id=Request::post('sku_id');
        $store_id=Request::post('store_id');
        $sku_info=Request::post('sku_info');
        $num=Request::post('num');
        $model = new ShoppingCartModel();
        $data=[];
        if($sid!=null){
            $data['sid']=$sid;
        }
        elseif ($uid!=null&&$gid!=null&&$sku_id!=null){
            try {
                ShoppingCartModel::where('uid', $uid)->where('gid', $gid)->where('sku_id', $sku_id)->delete();
            } catch (DbException $e) {
            }
        }
        if($uid!=null){
            $data['uid']=$uid;
        }
        if($gid!=null){
            $data['gid']=$gid;
        }
        if($sku_id!=null){
            $data['sku_id']=$sku_id;
        }
        if($store_id!=null){
            $data['store_id']=$store_id;
        }
        if($sku_info!=null){
            $data['sku_info']=$sku_info;
        }
        if($num!=null){
            $data['num']=$num;
        }
        if($sid!=null){
            $update = $model::find($sid);
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
        $destroy = ShoppingCartModel::destroy($id);
        if($destroy){
            return Result::get(200,null,'移除成功');
        }
        return Result::get(600,null,'移除失败');
    }
}