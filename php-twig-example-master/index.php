<?php

require_once('lib/Twig/Autoloader.php');
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('views');

$twig = new Twig_Environment($loader, array());

echo $twig->render('body.html', array(
									'name' => 'Blue', 
									'title' => 'Startpage',
									'product' => 'Shoes!'));
									

?>