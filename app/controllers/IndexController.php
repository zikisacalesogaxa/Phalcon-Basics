<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        require '../config/config.php';

        $redisConfig = $config['redis'];
        $redis = new \Credis_Client($redisConfig['host']);
        $users = Users::find();
        $usersArr = [];

        if ($redis->exists('users')) {
            # code...
            echo('hitting cache');
            $users = $redis->lrange('users', 0, -1);
        } else {
            echo('hitting database');
            foreach ($users as $user) {
                # code...
                $redis->rpush('users', $user->id . '=' . $user->name . '=' . $user->email);
            }
            $users = $redis->lrange('users', 0, -1);
        }

        $this->view->users = $users;
    }
}