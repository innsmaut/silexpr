<?php

require_once __DIR__.'/../vendor/autoload.php';

require_once 'dbConnection.php';

$app = new Silex\Application();

$app->get('/', function () use ($app) {
    $sql = new dbConnection();
    $result = $sql->dbRead([]);
    return require (__DIR__.'\view\main.php');
});

$app->get('/create', function () {
    $view = require (__DIR__.'\view\insert_links.php');
    return $view;
});

$app->post('/create', function () {
    if (isset($_POST['claimed_link'])){
        $redirectLink = md5($_POST['claimed_link'].date_timestamp_get(date_create()));
        $expiredOn = ($_POST['expired_on'] === '')?'':date_timestamp_get(date_add(date_create(),
            date_interval_create_from_date_string($_POST['expired_on'].' minutes')));
        $item = [
            'claimedLink' => $_POST['claimed_link'].'',
            'redirectLink' => $redirectLink,
            'password' => $_POST['password'],
            'expiredOn' => $expiredOn
        ];

        $sql = new dbConnection();
        $sql->dbWrite($item);

        $view = require (__DIR__.'\view\insert_links.php');
        return $view;
    }
})->after(function (){unset($_POST['claimed_link'], $_POST['expired_on'], $_POST['password']);});

$app->get('/{link}', function ($link) use ($app){
    $sql = new dbConnection();
    $item = ['claimedLink' => $link,];
    $result = $sql->dbRead($item);
    if (!isset($_POST['password_acc'])){
        if (!isset($result['error'])){
            if ($result[0]['password'] != '') {
                return require (__DIR__.'\view\password.php');
            } else {
                return $app->redirect($result[0]['claimed_link']);
            }
        }
    } 
    return $app->abort(404, $result['error']);
});

$app->post('/{link}', function ($link) use ($app){
    if (isset($_POST['password_acc'])){
        $sql = new dbConnection();
        $item = [
            'claimedLink' => $link,
            'password' => $_POST['password_acc']
        ];
        unset($_POST['password_acc']);
        $result = $sql->dbRead($item);
        if (!isset($result['error'])){
            return $app->redirect($result[0]['claimed_link']);
        } else {
            return $app->abort(404, $result['error']);
        }
    } else {
        return $app->abort(404, "Wrong link!");
    }
});

$app['debug'] = true;

$app->run();
