<?php
declare(strict_types=1);

use Symfony\Component\Console\Application;

$container = include('bootstrap/app.php');

$application = new Application('ProfoundBot9004', '1.0.0');

//$application->add($container->get(\App\Console\Facebook\PostImage::class));
$application->add($container->get(\App\Console\Reddit\RandomEarthPorn::class));
$application->add($container->get(\App\Console\ChartLyrics\GetRandomLyrics::class));
$application->add($container->get(\App\Console\Facebook\PostImage::class));
$application->add($container->get(\App\Console\ImageGenerator::class));

$application->run();
