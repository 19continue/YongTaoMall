<?php

namespace app\manage\controller;

use app\manage\model\CategorizeModel;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Request;
use app\BaseController;

class Categorize extends BaseController
{
    public function get(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $class_id=Request::post('class_id');
        $name=Request::post('name');
        $status=Request::post('status');
        $model = new CategorizeModel();
        $baseQuery = $model::where('parent', '=', 0);
        if($class_id!=null){
            $baseQuery->where('class_id','=',$class_id);
        }
        if($name!=null){
            $baseQuery->where('name','like','%'.$name.'%');
        }
        if($status!=null){
            $baseQuery->where('status','=',$status);
        }
        try {
            $count = $baseQuery->count('class_id');
            $baseQuery->order(['sort desc','class_id desc']);
            if($pageSize!=null && $currentPage!=null){
                $pageSize=(int)$pageSize;
                $currentPage=(int)$currentPage;
                if($currentPage<1){
                    $currentPage=1;
                }
                $baseQuery->limit(($currentPage - 1) * $pageSize, $pageSize);
            }
            $data = $baseQuery->select();
            foreach ($data as $k=>$v){
                $children=$model::where('parent','=',$v->class_id)->select();
                if(count($children)>0) {
//                    $count+=count($children);
                    $data[$k]->children = $children;
                }
            }
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600,json_encode($e->getTrace()),'查询失败');
        }
        return Result::get(200,['records'=>$data,'total'=>$count],'查询成功');
    }

    public function getTop(): \think\response\Json
    {
        $model = new CategorizeModel();
        try {
            $data = $model::where('parent', '=', 0)->field(['class_id','name'])->select();
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600,json_encode($e->getTrace()),'查询失败');
        }
        return Result::get(200,$data,'查询成功');
    }

    public function save(): \think\response\Json
    {
        $class_id=Request::post('class_id');
        $name=Request::post('name');
        $status=Request::post('status');
        $sort=Request::post('sort');
        $picture=request()->file('picture');
        $parent=Request::post('parent');
        $model = new CategorizeModel();
        $data=[];
        if($name!=null){
            $data['name']=$name;
        }
        if($status!=null){
            $data['status']=$status;
        }
        if($sort!=null){
            $data['sort']=$sort;
        }
        if($parent!=null){
            $data['parent']=$parent;
        }
        if($picture!=null){
            $save_name = \think\facade\Filesystem::disk('public')->putFile( 'categorize', $picture);
            $path='http://localhost/public/storage/'.$save_name;
            $data['picture']=$path;
        }
        if($class_id!=null){
            $update = $model::find($class_id);
            $save = $update->save($data);
            if($save){
                return Result::get(200,null,'保存成功');
            }
        }else{
            $save = $model->save($data);
            if($save){
                return Result::get(200,null,'新建成功');
            }
        }
        return Result::get(600,null,'操作失败');
    }
    public function delete(): \think\response\Json
    {
        $id=Request::post('id','[]');
        $id=json_decode($id);
        $destroy = CategorizeModel::destroy($id);
        if($destroy){
            return Result::get(200,null,'删除成功');
        }
        return Result::get(600,null,'删除失败');
    }
}