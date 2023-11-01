<?php

namespace app\manage\controller;

use app\manage\model\CarouselModel;
use app\manage\model\CategorizeModel;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Request;
use app\BaseController;

class Carousel extends BaseController
{
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $id=Request::post('id');
        $status=Request::post('status');
        $model = new CarouselModel();
        $baseQuery = $model::field('id,src,url,sort,fit,status');
        if($id!=null){
            $baseQuery->where('class_id','=',$id);
        }
        if($status!=null){
            $baseQuery->where('status','=',$status);
        }
        try {
            $count = $baseQuery->count('id');
            $baseQuery->order(['sort desc','id desc']);
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
        $id=Request::post('id');
        $src=request()->file('src');
        $url=Request::post('url');
        $sort=Request::post('sort');
        $fit=Request::post('fit');
        $status=Request::post('status');
        $model = new CarouselModel();
        $data=[];
        if($src!=null){
            $save_name = \think\facade\Filesystem::disk('public')->putFile( 'categorize', $src);
            $path='http://localhost/public/storage/'.$save_name;
            $data['src']=$path;
        }
        if($url!=null){
            $data['url']=$url;
        }
        if($fit!=null){
            $data['fit']=$fit;
        }
        if($status!=null){
            $data['status']=$status;
        }
        if($sort!=null){
            $data['sort']=$sort;
        }
        if($id!=null){
            $update = $model::find($id);
            $save = $update->save($data);
            if($save){
                return Result::get(200,null,'保存成功');
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
        $destroy = CarouselModel::destroy($id);
        if($destroy){
            return Result::get(200,null,'删除成功');
        }
        return Result::get(600,null,'删除失败');
    }
}