<?php

namespace Acelle\Console\Commands;

use Illuminate\Console\Command;

class ImportList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:import {--list-uid=} {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import subscribers to mail list';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $list_uid = $this->option('list-uid');
        $path = $this->option('path');

        if (empty($list_uid)) {
            echo "ERROR: please specify mail list UID: --list-uid\n";
            exit(1);
        }

        if (empty($path)) {
            echo "ERROR: please specify input file's path: --path\n";
            exit(1);
        }

        if (!file_exists($path)) {
            echo "ERROR: file $path does not exist.\n";
            exit(1);
        }

        $list = \Acelle\Model\MailList::findByUid($list_uid);
        $list->import3($path);
    }
}
