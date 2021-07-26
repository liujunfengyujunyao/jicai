<?php

namespace app\admin\behavior;

class AdminLog
{
    public function run(&$params)
    {
//        halt($params);
        if (request()->isPost()) {
            \app\admin\model\AdminLog::record();
        }
    }
}
