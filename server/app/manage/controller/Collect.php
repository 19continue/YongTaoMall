<?php

namespace app\manage\controller;

use app\manage\model\CollectModel;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Request;
use app\BaseController;

class Collect extends BaseController
{
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $uid=Request::post('uid');
        $baseQuery =Db::table('collect')
            ->leftJoin('goods','collect.gid=goods.gid')
            ->leftJoin('goods_img','collect.gid=goods_img.gid and goods_img.first=1')
            ->field('goods.gid,goods.goods_name,goods.o_price,goods.s_price,goods.total,goods.sales,goods.status,goods.store,goods.delete_status,goods_img.src');
        if($uid!=null){
            $baseQuery->where('uid','=',$uid);
        }
        try {
            $count = $baseQuery->count('collect.id');
            $baseQuery->order('collect.id desc');
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
        $uid=Request::post('uid');
        $gid=Request::post('gid');
        $model = new CollectModel();
        $data=[];
        $data['uid']=$uid;
        $data['gid']=$gid;
        $save = $model->save($data);
        if($save){
            return Result::get(200,null,'收藏成功');
        }
        return Result::get(600,null,'收藏失败');
    }

    public function hasCollected(): \think\response\Json
    {
        $uid=Request::post('uid');
        $gid=Request::post('gid');
        try {
            $count = CollectModel::where('uid', $uid)->where('gid', $gid)->count();
            return Result::get(200,$count,'查询成功');
        } catch (DbException $e) {
            return Result::get(600,0,'查询失败');
        }
    }

    public function delete(): \think\response\Json
    {
        $uid=Request::post('uid');
        $gid=Request::post('gid');
        try {
            $destroy = CollectModel::where('uid', $uid)->where('gid', $gid)->delete();
            if($destroy){
                return Result::get(200,null,'移除成功');
            }
        } catch (DbException $e) {
        }
        return Result::get(600,null,'移除失败');
    }
}