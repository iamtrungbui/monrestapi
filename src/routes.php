<?php
$prefix = env('MONRESTAPI_PREFIX_URL', 'api');
$this->app->route->group(['prefix' => $prefix], function ($router) {
    $router->post('upload', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@upload',
    ]);
    $router->get('{entity}', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@get',
    ]);
    $router->get('{entity}/{id:[0-9]+}', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@show',
    ]);
    $router->post('{entity}', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@store',
    ]);
    $router->put('{entity}/{id:[0-9]+}', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@update',
    ]);
    $router->patch('{entity}/{id:[0-9]+}', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@patch',
    ]);
    $router->delete('{entity}/{id:[0-9]+}', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@destroy',
    ]);
    $router->delete('{entity}', [
        'uses' => 'Iamtrungbui\Monrestapi\Controllers\APIController@destroyBulk',
    ]);
});
