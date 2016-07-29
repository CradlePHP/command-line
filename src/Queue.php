<?php //-->

//activated by: queue random-email value=1&value=2

namespace Cradle\CommandLine;

class Queue
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
		if(!file_exists($this->cwd . '/bootstrap.php')) {
			Index::error('This command requires ' . $this->cwd . '/bootstrap.php to be present.');
		}
		
		if(count($args) < 3) {
            Index::error('Not enough arguments. Usage: cradle job random-mail "?subject=hi&body=hello..."', true);
        }
		
		$data = array();
		
		if(strpos($args[2], '?') === 0) {
			parse_str(substr($args[2], 1), $data);
		} else {
			$data = json_decode($args[2], true);
		}
		
		$cradle = include($this->cwd . '/bootstrap.php');
		
		if(!$cradle && !function_exists('cradle')) {
			Index::error('$cradle ws not returned in bootstrap nor is there a cradle() function.');
		}
		
		if(!$cradle) {
			$cradle = cradle();
		}
		
		//run preprocesses
		$cradle->prepare();
		
		//now queue
		$cradle->package('global')->queue($args[1], $data);
		
		Index::success('`'.$args[1].'` has been successfully queued.');
    }
}