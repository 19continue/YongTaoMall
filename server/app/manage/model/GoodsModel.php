<?php

namespace app\manage\model;

use think\Model;

class GoodsModel extends Model
{
    protected $name = 'goods';
    protected $pk = 'gid';
    public function getDetailAttr($value): string
    {
        return htmlspecialchars_decode($value,ENT_HTML5);
    }

}