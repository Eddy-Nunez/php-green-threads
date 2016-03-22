<?php
/**
 * List of worker callback and helper functions.
 * 
 * NOTE: $acc is an "accumulator". This value is used to essentially
 * control the execution status aswell as to pass relevant callback data.
 * If value return is ever "false", the function that called the callback
 * will consider the work complete.
 */

/**
 * Helper function to announce completion of work.
 * @param string    $msg    message parameter
 * @param bool      $ret    return value to use
 * @return mixed
 */
function work_finished($msg, $ret = false) {
    printf( "Worker finished - %s\n", $msg);
    return $ret;
}

/**
 * Worker callback to print something.
 * Exit condition: when a random number is divisible by 133
 * 
 * @param mixed     $acc    accumulator value (see header note)
 * @return mixed
 */
function printWork( $acc ) {
    if ( ($n = rand() % 133) == 0 ) 
        $acc = work_finished("printWork");
    else
        printf( "Invoked with %s, n=%d".PHP_EOL, json_encode($acc), $n );
    return $acc;
}

/**
 * Worker callback to read a file and print out it's contents.
 * 
 * @param mixed     $acc    accumulator value (see header note)
 *                          if array, options:
 *                          filename: filename to read from.
 * @return mixed
 */
function fileWork( $acc ) {
    static $fp;
    is_array( $acc ) && extract( $acc );
    if (!$fp && $filename) {
        $fp = fopen($filename, 'r');
    }
    if (is_resource($fp)) {
        //$buffer .= fgets( $fp );
        @$acc['buffer'] .= fgets( $fp );
        if ( feof($fp) ) {
            fclose( $fp );
            print "File Buffer: " . $buffer . PHP_EOL;
            $acc = work_finished("fileWorker file = $filename, filesize: "
                 . strlen($acc['buffer']) );
        }
    } else {
        $acc = work_finished("error opening file: $filename");
    }
    return $acc;
}
