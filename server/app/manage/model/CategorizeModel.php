<?php

namespace app\manage\model;

use think\Model;

class CategorizeModel extends Model
{
    protected $name = 'categorize';
    protected $pk = 'class_id';
    public function goodsImg(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(GoodsImgModel::class,CategorizeGoodsModel::class,'gid','class_id')->where('first','=',1)->field('src');
    }
    public function goods(): \think\model\relation\BelongsToMany
    {
        return $this->belongsToMany(GoodsModel::class,CategorizeGoodsModel::class,'gid','class_id');
    }
}