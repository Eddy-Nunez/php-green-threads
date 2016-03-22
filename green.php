<?php

class GreenThreadPool
{
    static    $pool, $done;
    
    /**
     * Constructor.
     * Initializes the thread pool and done tracking lists.
     */
    public function __construct()
    {
        if ( !isset(self::$pool) ) {
            self::$pool = array();
            self::$done = array();
        }
    }

    /**
     * Add a new callable structure to the thread pool.
     * 
     * @param  callable     $callable takes a PHP callable style value.
     * @param  array        $args     parameters for the callable.
     * @return GreenThreadPool  for chaining more methods / fluent interface.
     */
    public function add( $callable, array $args )
    {
        if ( is_callable( $callable ) )
            self::$pool[] = $this->genWrapper( $callable, $args );

        return $this;
    }

    /**
     * Returns the completion status of the thread pool.
     * 
     * @return bool
     */
    public function isDone() {
        return empty(self::$pool);
    }

    /**
     * Invokes all the generators, invoking each a specified number of cycles.
     * 
     * @param  int  $runCycles      Number of sequential times each generator gets invoked.
     * @return void
     */
    public function run( $runCycles = 2 )
    {
        $iterations = (int) $runCycles;

        foreach ( self::$pool as $key => $generatorWrapper )
        {
            while ( $iterations-- ) {
                if ( $generatorWrapper->valid() )
                    $generatorWrapper->next();
                else {
                    self::$done[$key] = $generatorWrapper;
                    unset( self::$pool[$key] );
                    break;
                }
            }
            $iterations = (int) $runCycles;
        }
    }

    /**
     * Wraps a PHP callable in a generator.
     * 
     * Special note: the results of the callback are used as an "carryover"
     * value. The results of the previous iteration are passed to the next
     * iteration to allow the callback function to track it's own progress.
     * If callback function returns bool false, it is assumed to be finished
     * and the generator is allowed to end.
     * 
     * @param  callable     $callable   PHP callable construct.
     * @param  array        $args       Callable's parameters.
     * @return Generator
     */
    protected function genWrapper( $callable, array $args ) {
        $result = $args;
        while ( FALSE !== $result ) {
            $result = call_user_func_array( $callable, [ $result ] );
            yield $result;
        }
    }
}
