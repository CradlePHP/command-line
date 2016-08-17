<?php //-->
/**
 * This file is part of the Cradle PHP Command Line
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Cradle\CommandLine;

use Cradle\Framework\Exception;
use Cradle\Framework\Decorator;

//enable the function
Decorator::DECORATE;

/**
 * Uninstall CLI Command
 *
 * @vendor   Scoop
 * @package  Framework
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Package
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
        if(count($args) < 3) {
            Index::error('Not enough arguments. Usage: cradle package vendor/package command');
        }

        $cradle = cradle();
        if(file_exists($this->cwd . '/bootstrap.php')) {
            include($this->cwd . '/bootstrap.php');
        }

        try {
            $cradle->package($args[1]);
        } catch(Exception $e) {
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
        list($author, $package) = explode('/', $args[1], 2);
        $cradle->trigger($author . '-' . $package . '-' . $args[2], $this->cwd, $args);

        Index::info($args[2] .' has successfully completed.');
    }
}
