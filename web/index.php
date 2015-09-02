<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});
$app->get('/invite', function(Request $request) use($app) {
  $data = array('user_id' => 'Your ID');
  $form = $app['form.factory'] -> createBuilder('form',$data)
	-> add('user_id')
	-> getForm();
  $form -> handleRequest($request);
  return $app['twig'] -> render('invite.twig'
	array('form' => $form->createView());
});

$app->run();
