<?php

include 'green.php';
include 'workers.php';

$pool = new GreenThreadPool;
$pool->add( 'printWork', array() );
$pool->add( 'fileWork', [ 'filename' => 'workers.php' ] );

while ( !$pool->isDone() )
    $pool->run();

