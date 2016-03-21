<?php
declare( strict_types=1 );
include 'workers.php';

/**
 * Controls the execution of the callback method.
 * It expects the callback to return false to signal it's completion.
 * 
 * $args serves two roles:
 * - controls when the function ends, false value ends it all
 * - used an input and output of the callback
 */
function workerGenerator( int $cycles, $callback, array $args ) {
    //$accumulator = true;
    $c = $cycles; // retain how many cycles the worker does
    do {
        while ( $c-- > 0 ) {
            if ($args && is_callable($callback))
                $args = call_user_func_array($callback, [$args]);
            //$json = json_encode($args);
            //print "in workGen [$callback] c=$c acc=$json\n";
        }
        $c = $cycles; // restore $c
        yield;
    } while ( false !== $args );
}

$pool = [
            workerGenerator( 2, 'printWork', ['eddy']), 
            workerGenerator( 1, 'fileWork', ['filename' => 't2.php'])
        ];
$i = 1;
while ( ! empty($pool) )
{
    foreach ( $pool as $k => $worker ) {
        if ( $worker->valid() )
            $worker->next();
        else
            unset( $pool[$k] );
    }
    printf("BIG LOOP @ %d, s=%d\n", $i++, count($pool));
}
print "ALL DONE!" . PHP_EOL;