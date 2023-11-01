<?php

namespace app\manage\controller;

use app\BaseController;
use app\manage\model\SkuModel;

class Sku extends BaseController
{
    protected $model=null;
    public function initialize()
    {
        parent::initialize();
        $this->model = new SkuModel();
    }

}