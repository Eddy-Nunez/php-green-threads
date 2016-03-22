<?php

include 'green.php';
include 'workers.php';

$pool = new GreenThreadPool;
$pool->add( 'printWork', array() );
$pool->add( 'fileWork', [ 'filename' => 'workers.php' ] );

// $pool->add( function($acc) {
//     $sec = date('s');
//     print "secs = $sec" . PHP_EOL;
//     sleep(1);
//     return !($sec >= 55 && $sec <= 59);
// }, [] );

while ( !$pool->isDone() )
    $pool->run();

