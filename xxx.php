<?php

include 'green.php';
include 'workers.php';

$pool = new GreenThreadPool;
$pool->add( 'printWork', [ true ] );
$pool->add( 'fileWork', [ ['filename' => 'workers.php'] ] );
$pool->run();

