<?php //-->
/**
 * This file is part of the Cradle PHP Command Line
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Cradle\CommandLine;

use Cradle\Frame\FrameException;

/**
 * Uninstall CLI Command
 *
 * @vendor   Scoop
 * @package  Framework
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Uninstall
{
    /**
     * @var string|null $cwd The path from where this was called
     */
    protected $cwd = null;

    /**
     * We need the CWD and the Schema
     *
     * @param string $cwd The path from where this was called
     */
    public function __construct($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * Runs the CLI Generate process
     *
     * @param array $args CLI arguments
     *
     * @return void
     */
    public function run($args)
    {
		if(!file_exists($this->cwd . '/bootstrap.php')) {
			Index::error('This command requires ' . $this->cwd . '/bootstrap.php to be present.');
		}

		//expecting cradle install cradle/address
		if(count($args) < 2) {
            Index::error('Not enough arguments.', 'Usage: cradle uninstall vendor/package');
        }
		
		list($author, $package) = explode('/', $args[1], 2);
        
		$cradle = include($this->cwd . '/bootstrap.php');
		
		if(!$cradle && !function_exists('cradle')) {
			Index::error('$cradle ws not returned in bootstrap nor is there a cradle() function.');
		}
		
		if(!$cradle) {
			$cradle = cradle();
		}
		
		try {
			$cradle->package($args[1]);
		} catch(FrameException $e) {
			//if it's a frame exception 
			//it means that the package wasn't registered
			$cradle->register($args[1]);
		}
		
		//start CLI mode
		$cradle
			->error(function($request, $response, $error) {
				Index::error($error->getMessage() . PHP_EOL . $error->getTraceAsString());
			})
			->prepare();
		
		//simply trigger
		$cradle->trigger($author . '-' . $package . '-uninstall');
			
		Index::info($args[1] .' has successfully uninstalled.');
    }
}