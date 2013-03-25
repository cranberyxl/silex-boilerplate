<?php

use Silex\Application;

// Mongo Set
$app['app.controller'] = $app->share(function(Application $app) {
    return new AppController($app);
});
$app->match('/app/edit//{id}', 'app.controller:formAction')->bind('app_form')->value('id', null);
$app->post('/app/delete/{id}', 'app.controller:deleteAction')->bind('app_delete');
$app->get('/app/{name}', 'app.controller:viewAction')->bind('app_view');
