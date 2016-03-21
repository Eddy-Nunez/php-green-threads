<?php

class GreenThreadPool
{
    static $pool, $args;
    
    public function __construct()
    {
        if ( !isset($pool) ) {
            self::$pool = array();
            self::$args = array();
        }
    }

    public function add( $callable, array $args )
    {
        if ( is_callable( $callable ) )
        {
            self::$pool[] = $callable;
            self::$args[] = $args;
        }
    }

    public function run()
    {
        $done       = array();
        $pool_count = count(self::$pool);
        while ( count($done) < $pool_count )
        {
            foreach (self::$pool as $i => $callable)
            {
                if ( ! @$done[$i] )
                    $result = call_user_func_array($callable, self::$args[$i]);
                if ( false === $result )
                    $done[$i] = true; // mark pool worker as done
                else
                    self::$args[$i] = [ $result ]; // carry into next iteration
            }
        }
    }
}
