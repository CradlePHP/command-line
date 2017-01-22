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

use Cradle\Http\Request;
use Cradle\Http\Response;

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
class Event
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
            Index::error('Not enough arguments. Usage: cradle event name');
        }

        $cradle = cradle();
        if(file_exists($this->cwd . '/bootstrap.php')) {
            include($this->cwd . '/bootstrap.php');
        }

        //Setup a default error handler
        $cradle->error(function($request, $response, $error) {
            Index::error($error->getMessage() . PHP_EOL . $error->getTraceAsString());
        });

        //prepare data
        $event = $args[1];

        $data = Index::parseArgs(array_slice($args, 2));

        if (isset($data['__json'])) {
            $json = $data['__json'];
            unset($data['__json']);

            $data = array_merge(json_decode($json, true), $data);
        }

        if (isset($data['__json64'])) {
            $base64 = $data['__json64'];
            unset($data['__json64']);

            $json = base64_decode($base64);
            $data = array_merge(json_decode($json, true), $data);
        }

        if (isset($data['__query'])) {
            $query = $data['__query'];
            unset($data['__query']);

            parse_str($query, $query);

            $data = array_merge($query, $data);
        }

        //set the the request and response
        $request = $cradle->getRequest();
        $response = $cradle->getResponse();

        $request->setStage($data);

        //see HttpTrait->render() for similar implementation
        //if prepared returned false
        if (!$cradle->prepare()) {
            //dont do anything else
            return $this;
        }

        if ($response->getStatus() == 200) {
            $continue = $cradle
                ->trigger($event, $request, $response)
                ->getEventHandler()
                ->getMeta();

            if(!$continue) {
                return $this;
            }
        }

        if (!$response->hasContent() && $response->hasJson()) {
            $json = json_encode($response->get('json'));
            $response->setContent($json);
        }

        if ($response->hasContent()) {
            echo $response->getContent();
        } else {
            Index::info($args[1] .' has successfully completed.');
        }

        //the connection is already closed
        //also remember there are no more sessions
        $cradle->shutdown();
    }
}
