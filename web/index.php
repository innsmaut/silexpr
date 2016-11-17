<?php

require_once __DIR__.'/../vendor/autoload.php';

require_once 'dbConnection.php';

$app = new Silex\Application();

$app->get('/srv', function () {
    $dt = date_timestamp_get(date_add(date_create(), date_interval_create_from_date_string('156 minutes')));
    echo $dt;
    return true;
});

$app->get('/', function () {
    $view = require (__DIR__.'\view\insert_links.php');
    return $view;
});

$app->post('/', function () {
    if (isset($_POST['claimed_link']) && isset($_POST['password'])){
        $redirectLink = md5($_POST['claimed_link']);
        $cfg = require (__DIR__.'\config.php');
        $item = [
            'claimdedLink' => $_POST['claimed_link'],
            'redirectLink' => $redirectLink,
            'password' => $_POST['password'],
            'expired_on' => $_POST['expired_on']
        ];
        $sql = new dbConnection($cfg['db']);
        $sql->dbWrite($item);

        $view = require (__DIR__.'\view\insert_links.php');
        return $view;
    }
});

$app->get('/{link}', function ($link) use ($app){
    $cfg = require (__DIR__.'\config.php');
    $sql = new dbConnection($cfg['db']);
    $item = ['claimedLink' => $link,];
    $result = $sql->dbRead($item);
    if (!isset($_POST['password_acc'])){
        if (!isset($result['error'])){
            if ($result['password'] != '') {
                return require (__DIR__.'\view\password.php');
            } else {
                return $app->redirect($result['claimed_link']);
            }
        }
    } else {
        return $app->abort(404, $result['error']);
    }
    return $result['error'];
});

$app->post('/{link}', function ($link) use ($app){
    if (isset($_POST['password_acc'])){
        $cfg = require (__DIR__.'\config.php');
        $sql = new dbConnection($cfg['db']);
        $item = [
            'claimedLink' => $link,
            'password' => $_POST['password_acc']
        ];
        unset($_POST['password_acc']);
        return $app->redirect($sql->dbRead($item)['claimed_link']);
    } else {
        return $app->abort(404, "Wrong link or password!");
    }
    return 'TEST';
});

//$app['debug'] = true;

$app->run();
