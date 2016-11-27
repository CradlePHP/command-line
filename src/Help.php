<?php //-->

//activated by: help

namespace Cradle\CommandLine;

class Help
{
    /**
     * @var string|null $cwd The path from where this was called
     */
    protected $cwd = null;

    /**
     * We need the CWD
     *
     * @param string $cwd The path from where this was called
     */
    public function __construct($cwd)
    {
        $this->cwd = $cwd;
    }

    /**
     * Runs the CLI process
     *
     * @param array $args CLI arguments
     *
     * @return mixed
     */
    public function run(array $args)
    {
        Index::info('Usage: `cradle package <vendor/package> <command>`  - Runs a package event');
        Index::info('Usage: `cradle <vendor/package> <command>`          - Runs a package event');
        Index::info('Usage: `cradle event <event name> <json|query>`     - Runs an event');
        Index::info('Usage: `cradle <event name> <json|query>`           - Runs an event');
    }
}
