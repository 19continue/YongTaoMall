<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\AttrValueModel;


class SkuValue extends BaseController
{
    protected $model=null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new AttrValueModel();
    }

}