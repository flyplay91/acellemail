<?php

/**
 * RunHandler class.
 *
 * CLI interface for trigger email handling by cronjob (bounce, feedback)
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   Console App
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;
use Acelle\Model\BounceHandler;
use Acelle\Model\FeedbackLoopHandler;
use Acelle\Library\Log;

class RunHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handler:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->execRunHandler();
            Log::info('Handlers finished!');
        } catch (\Exception $e) {
            Log::error('Something went wrong: '.$e->getMessage());
        }
    }
    
    /**
     * Actually run the handler.
     *
     * @return mixed
     */
    private function execRunHandler()
    {
        // guarantee that only one process can be run at one time
        // use socket as lock
        Log::info('Try to start handling process...');
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (false === $socket) {
            throw new Exception("can't create socket: ".socket_last_error($socket));
        }

        // hide warning, because error will be checked manually
        if (false === @socket_bind($socket, '127.0.0.1', 60204)) {
            // some other process is already running
            Log::warning('Another handling process is already running. Terminated!');
            exit;
        } else {
            // just go ahead
            Log::info('Started!');
        }

        // abuse
        $handlers = FeedbackLoopHandler::get();
        Log::info(sizeof($handlers).' feedback loop handlers found');
        $count = 1;
        foreach ($handlers as $handler) {
            Log::info('Starting handler '.$handler->name." ($count/".sizeof($handlers).')');
            $handler->start();
            Log::info('Finish processing handler '.$handler->name);
            $count += 1;
        }

        // bounce
        $handlers = BounceHandler::get();
        Log::info(sizeof($handlers).' bounce handlers found');
        $count = 1;
        foreach ($handlers as $handler) {
            Log::info('Starting handler '.$handler->name." ($count/".sizeof($handlers).')');
            $handler->start();
            Log::info('Finish processing handler '.$handler->name);
            $count += 1;
        }
    }
}
