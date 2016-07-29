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
        Index::info('Help Menu');
        Index::info('- `cradle install <*vendor>/<*package>`      Installs a Package');
        Index::info('- `cradle uninstall <*vendor>/<*package>`    Uninstalls a Package');
        Index::info('- `cradle job <name*> <data*>`               Executes a job');
		Index::info('- `cradle queue <name*> <data*>`             Queues a job');
    }
}