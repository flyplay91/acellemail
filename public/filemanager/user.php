<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

function getUid()
{
    require '../../bootstrap/autoload.php';
    $app = require_once '../../bootstrap/app.php';

    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');

    $response = $kernel->handle(
      $request = Illuminate\Http\Request::capture()
    );
    
    $id = $app['encrypter']->decrypt($_COOKIE[$app['config']['session.cookie']]);
    $app['session']->driver()->setId($id);
    $app['session']->driver()->start();
    
    $user_uid = $app['auth']->user()->uid;
    
    // Make folder if not exist
    $source_path = '../source/' . $user_uid;
    $thumbs_path = '../thumbs/' . $user_uid;
    if (!file_exists($source_path)) {
        mkdir($source_path, 0777, true);
    }
    if (!file_exists($thumbs_path)) {
        mkdir($thumbs_path, 0777, true);
    }
    
    return $user_uid;
}

echo getUid();
?>