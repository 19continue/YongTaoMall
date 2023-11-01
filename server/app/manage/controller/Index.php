<?php
namespace app\manage\controller;

use app\BaseController;
use app\manage\model\AttrValueModel;
use app\manage\model\CategorizeGoodsModel;
use app\manage\model\CategorizeModel;
use app\manage\model\GoodsImgModel;
use app\manage\model\GoodsModel;
use app\manage\model\SkuAttrModel;
use app\manage\model\SkuModel;
use app\manage\model\StoreModel;
use app\Result;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Request;

class Index extends BaseController
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V' . \think\facade\App::version() . '<br/><span style="font-size:30px;">16载初心不改 - 你值得信赖的PHP框架</span></p><span style="font-size:25px;">[ V6.0 版本由 <a href="https://www.yisu.com/" target="yisu">亿速云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ee9b1aa918103c4fc"></think>';
    }

    public function get_category_product(): \think\response\Json
    {
        $pageSize=Request::post('pageSize');
        $currentPage=Request::post('currentPage');
        $db = Db::table('categorize')
            ->where('categorize.parent', '=', 0)
            ->where('categorize.status', '=', 1)
            ->order(['categorize.sort desc', 'categorize.class_id desc']);

        if($pageSize!=null && $currentPage!=null){
            $pageSize=(int)$pageSize;
            $currentPage=(int)$currentPage;
            if($currentPage<1){
                $currentPage=1;
            }
            $db->field('categorize.*')
                ->limit(($currentPage - 1) * $pageSize, $pageSize);
        }
        try {
            $data = $db->select();
            $data=$data->toArray();
            foreach ($data as $k=>$v){
                $goods = Db::table('categorize_goods')
                    ->where('categorize_goods.class_id', '=', $v['class_id'])
                    ->join('goods', 'categorize_goods.gid = goods.gid and goods.status = 1 and goods.delete_status = 1')
                    ->leftJoin('goods_img', 'goods.gid = goods_img.gid and goods_img.first = 1')
                    ->order(['goods.sort desc', 'goods.gid desc'])
                    ->field('goods.gid,goods.goods_name,goods.o_price,goods.s_price,goods.total,goods.sales,goods_img.src')
                    ->limit(12)->select();

                $data[$k]['goods']=$goods;
            }
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600,json_encode($e->getTrace()),"查询失败");
        }
        return Result::get(200,$data,"查询成功");
    }
    public  function search(): \think\response\Json
    {
        $pageSize = Request::post('pageSize');
        $currentPage = Request::post('currentPage');
        $key_words = Request::post('key_words');
        $order_by = Request::post('order_by');
        $order_arr = [];
        $goods_name='%';
        $db = Db::table('goods')->field('goods.gid,goods.goods_name,goods.o_price,goods.s_price,goods.total,goods.sales,goods_img.src')
            ->leftJoin('goods_img', 'goods.gid = goods_img.gid and goods_img.first = 1');
        if ($key_words != null && $key_words !='') {
            for ($i=0;$i<strlen($key_words);$i++){
                $goods_name=$goods_name.$key_words[$i];
            }
//            $goods_name = implode('%', $chars);
            $db->where('goods.goods_name','like',$goods_name.'%');
        }
        else{
            return Result::get(200, ['records'=>[],'total'=>0], "查询成功");
        }
        if ($order_by != null) {
            if ($order_by == 'price-asc') {
                $order_arr[] = 'goods.s_price asc';
            } elseif ($order_by == 'price-desc') {
                $order_arr[] = 'goods.s_price desc';
            } elseif ($order_by == 'sales') {
                $order_arr[] = 'goods.sales desc';
            }
        }
        $order_arr[] = 'goods.sort desc';
        $order_arr[] = 'goods.gid desc';
        try {
            $count = $db->count('goods.gid');
            $db->order($order_arr)->where('goods.status', 1)->where('goods.delete_status',1);
            if ($pageSize != null && $currentPage != null) {
                $pageSize = (int)$pageSize;
                $currentPage = (int)$currentPage;
                if ($currentPage < 1) {
                    $currentPage = 1;
                }
                $db->limit(($currentPage - 1) * $pageSize, $pageSize);
            }
            $data = $db->select();
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600, json_encode($e->getTrace(),JSON_INVALID_UTF8_IGNORE), "查询失败");
        }
        return Result::get(200, ['records'=>$data,'total'=>$count,'key'=>$goods_name], "查询成功");
    }
    public  function get_product(): \think\response\Json
    {
        $pageSize = Request::post('pageSize');
        $currentPage = Request::post('currentPage');
        $class_id = Request::post('class_id');
        $order_by = Request::post('order_by');
        $order_arr = [];
        $db = Db::table('goods')->field('goods.gid,goods.goods_name,goods.o_price,goods.s_price,goods.total,goods.sales,goods_img.src')
            ->leftJoin('goods_img', 'goods.gid = goods_img.gid and goods_img.first = 1');
        if ($class_id != null) {
            $db->join('categorize_goods', 'categorize_goods.gid = goods.gid and categorize_goods.class_id = ' . $class_id);
        }
        if ($order_by != null) {
            if ($order_by == 'price-asc') {
                $order_arr[] = 'goods.s_price asc';
            } elseif ($order_by == 'price-desc') {
                $order_arr[] = 'goods.s_price desc';
            } elseif ($order_by == 'sales') {
                $order_arr[] = 'goods.sales desc';
            }
        }
        $order_arr[] = 'goods.sort desc';
        $order_arr[] = 'goods.gid desc';
        $db->order($order_arr)->where('goods.status', 1)->where('goods.delete_status',1);
        if ($pageSize != null && $currentPage != null) {
            $pageSize = (int)$pageSize;
            $currentPage = (int)$currentPage;
            if ($currentPage < 1) {
                $currentPage = 1;
            }
            $db->limit(($currentPage - 1) * $pageSize, $pageSize);
        }
        try {
            $data = $db->select();
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600, json_encode($e->getTrace()), "查询失败");
        }
        return Result::get(200, $data, "查询成功");
    }
    public function getProductInfo(): \think\response\Json
    {
        $gid = Request::get('gid');
        try {
            $goods =GoodsModel::field('goods.gid,goods.goods_name,goods.type,goods.o_price,goods.s_price,goods.total,goods.sales,goods.tid,goods.detail,goods.store')
                ->withAttr('detail', function ($value, $data) {
                    return htmlspecialchars_decode($value, ENT_HTML5);
                })->where('gid', $gid)->where('status',1)->where('delete_status',1)->find();
            if($goods==null){
                return Result::get(400, null, "查询不到该商品");
            }
            $picture=GoodsImgModel::field('src')
                ->where('gid', '=', $gid)
                ->order('sort asc')
                ->select();
            $attr=SkuAttrModel::where('gid','=',$gid)->order('attr_id asc')
                ->select();
            $attr_value=AttrValueModel::where('gid','=',$gid)->select();
            $sku=SkuModel::where('gid','=',$gid)
                ->select();
            $store=StoreModel::where('store_id','=',$goods->store)
                ->where('status',1)
                ->where('delete_status',1)
                ->field('store_id,store_name,avatar,sales,goods_score,service_score,logistics_score')
                ->find();
            $data=['goods'=>$goods,'picture'=>$picture,'attr'=>$attr,'attr_value'=>$attr_value,'sku'=>$sku,'store'=>$store];
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
            return Result::get(600, json_encode($e->getTrace()), "查询失败");
        }
        return Result::get(200, $data, "查询成功");
    }
}
