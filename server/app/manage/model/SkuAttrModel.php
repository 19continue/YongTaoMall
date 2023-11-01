<?php

namespace app\manage\model;

use think\Model;

class SkuAttrModel extends Model
{
    protected $name = 'sku_attr';
    protected $pk = 'attr_id';

    public function attr_value(): \think\model\relation\HasMany
    {
        return $this->hasMany(AttrValueModel::class,'attr_id');
    }
}