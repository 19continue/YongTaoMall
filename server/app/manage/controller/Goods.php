<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\CategorizeGoodsModel;
use app\manage\model\GoodsImgModel;
use app\manage\model\GoodsModel;
use app\manage\model\SkuAttrModel;
use app\manage\model\SkuModel;
use app\manage\model\AttrValueModel;
use app\Result;
use think\db\exception\DbException;
use think\facade\Db;
use think\facade\Request;
use think\response\Json;

class Goods extends BaseController
{
    protected $model=null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new GoodsModel();
    }
    public function getAll(): Json
    {
        $goods = Db::table('goods,goods_img')
            ->where('goods.gid=goods_img.gid')
            ->where('goods_img.first','>',0)
            ->where('goods.delete_status',1)
            ->field('goods.*,goods_img.src')
            ->order(['goods.gid desc','goods.sort desc'])->withAttr('detail',function ($value,$data){
                return htmlspecialchars_decode($value,ENT_HTML5);
            })->select();
        return Result::get(200,$goods,'查询成功');
    }
    public function insert(): Json
    {
        $goods_name=Request::post('goods_name','');
        $type=Request::post('type',0);
        $o_price=Request::post('o_price',0);
        $sales=0;
        $sort=Request::post('sort',0);
        $status=Request::post('status',0);
        $class_id=Request::post('class_id','[]');
        $tid=Request::post('tid',1);
        $detail=Request::post('detail','');
        $decode_detail=htmlspecialchars($detail,ENT_HTML5);
        $class_id=json_decode($class_id);
        $categorizeGoodsModel = new CategorizeGoodsModel();
        $categorizeGoods=[];
        $goods=$this->model::create([
            'goods_name'  =>  $goods_name,
            'type'  =>  $type,
            'o_price'  =>  $o_price,
            'sales'  =>  $sales,
            'sort'   =>  $sort,
            'status'  =>  $status,
            'tid'  =>  $tid,
            'detail'  =>  $decode_detail,
        ]);
        foreach ($class_id as $k=>$v){
            $categorizeGoods[]=['class_id'=>$v,'gid'=>$goods->gid];
        }
        try {
            $categorizeGoodsModel->saveAll($categorizeGoods);
        } catch (\Exception $e) {
            return Result::get(200,$goods,'商品分类添加失败!');
        }
        return Result::get(200,$goods,'添加成功!');
    }
    public function uploadSku(): Json
    {
        $gid=Request::post('gid');
        $sku=Request::post('sku');
        $attr=Request::post('attr','[]');
        $multi=Request::post('multi','true');
        $sku=json_decode($sku);
        $skuModel = new SkuModel();
        if($multi=='true'){
            $attr=json_decode($attr);
            $skuAttrModel=new SkuAttrModel();
            $skuValueModel=new AttrValueModel();
            $attr_arr=[];
            $value_arr=[];
            $sku_arr=[];
            $num=[];
            foreach ($attr as $k=>$v){
                $attr_arr[]=["gid"=>$gid,"key"=>$v->key];
                $n=count($v->value);
                if($k>0){
                    $num[]=$n+$num[$k-1];
                }
                else{
                    $num[]=$n;
                }
                foreach ($v->value as $i=>$j){
                    $value_arr[]=["gid"=>$gid,"attr_value"=>$j];
                }
            }
            try {
                $skuAttrData = $skuAttrModel->saveAll($attr_arr);
                $idx=0;
                foreach ($value_arr as $k=>$v){
                    if($k >= $num[$idx]) {
                        $idx = $idx + 1;
                    }
                    $value_arr[$k]["attr_id"]=$skuAttrData[$idx]->attr_id;
                }
                $skuValueData = $skuValueModel->saveAll($value_arr);
                $min_price=0;
                $total=0;
                foreach ($sku as $k=>$v){
                    if($min_price>$v->price || $min_price==0){
                        $min_price=$v->price;
                    }
                    $total+=(int)$v->amount;
                    $arr=$v->attr_path;
                    $path="-";
                    foreach ($arr as $i=> $j){
                        if($i==0){
                            $n=(int)$j;
                        }else{
                            $n=$num[$i-1]+(int)$j;
                        }
                        $path=$path.$skuAttrData[$i]->attr_id.":".$skuValueData[$n]->value_id."-";
                    }
                    $sku_arr[]=["gid"=>$gid,"price"=>$v->price,"amount"=>$v->amount,"status"=>$v->status,"attr_path"=>$path];
                }
                $skuData = $skuModel->saveAll($sku_arr);
                GoodsModel::update(['s_price' => $min_price,'total'=>$total], ['gid' => $gid]);
            } catch (\Exception $e) {
                return Result::get(600,json_encode($e->getTrace()),'添加失败!');
            }
            return Result::get(200,$skuData,'添加成功!');
        }
        else{
            $skuModel->save(["gid"=>$gid,"price"=>$sku->price,"amount"=>$sku->amount,"status"=>$sku->status]);
            GoodsModel::update(['s_price' => $sku->price,'total'=>$sku->amount], ['gid' => $gid]);
            return Result::get(200,null,'添加成功!');
        }
    }
    public function uploadImg(): Json
    {
        $files = request()->file('files');
        $gid = Request::param('gid');
        $path= [];
        foreach($files as $file){
            $url=  \think\facade\Filesystem::disk('public')->putFile( 'goods', $file);
            $path[]='http://localhost/public/storage/'.$url;
        }
        $goods_img=new GoodsImgModel();
        $new_goods=[];
        for($i=0;$i<count($path);$i++){
            $arr=array();
            $arr['gid']=$gid;
            $arr['sort']=$i;
            $arr['src']=$path[$i];
            if($i===0){
                $arr['first']=1;
            }
            else{
                $arr['first']=0;
            }
            $new_goods[]=$arr;
        }
        try {
            $goods_img->saveAll($new_goods);
        } catch (\Exception $e) {
            return Result::get(600,$e,'上传失败!');
        }
        return Result::get(200,$new_goods,'上传成功!');
    }
    public function uploadSkuImg(): Json
    {
        $files = request()->file('files');
        $id = Request::param('sku_id');
        $id=json_decode($id);
        $path= [];
        foreach($files as $file){
            $url=  \think\facade\Filesystem::disk('public')->putFile( 'sku', $file);
            $path[]='http://localhost/public/storage/'.$url;
        }
        $sku_img=new SkuModel();
        $sku=[];
        for($i=0;$i<count($path);$i++){
            $sku[]=["sku_id"=>$id[$i],"picture"=>$path[$i]];
        }
        try {
            $sku_img->saveAll($sku);
        } catch (\Exception $e) {
            return Result::get(600,json_encode($e->getTrace()),'上传失败!');
        }
        return Result::get(200,null,'上传成功!');
    }

    public function delete(): Json
    {
        $id=Request::post('id','[]');
        $id=json_decode($id);
        try {
            $destroy = GoodsModel::where('gid', 'in', $id)->update(['delete_status' => 0]);
            if($destroy){
                return Result::get(200,null,'删除成功!');
            }
        } catch (DbException $e) {
        }
        return Result::get(600,null,'删除失败');
    }
    public function change_status(): Json
    {
        $id=Request::post('id');
        $status=Request::post('status');
        if($id==null || $status==null){
            return Result::get(600, null, '缺少参数');
        }
        try {
            GoodsModel::where('gid', '=', $id)->update(['status' => $status]);
            return Result::get(200,null,'操作成功');
        } catch (DbException $e) {
            return Result::get(600, null, '操作失败');
        }
    }
}