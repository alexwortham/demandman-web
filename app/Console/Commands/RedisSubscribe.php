<?php

namespace App\Console\Commands;

use Redis;
use Illuminate\Console\Command;
use Event;
use App\Events\AppStartAckEvent;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

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
        Redis::psubscribe(['dm.*'], function($message, $channel) {
            echo "$message\n";
		//Here, use reflection to load event class from the json data.
		//Just make all events accept associative array and boom, done.
		Event::fire(new AppStartAckEvent(json_decode($message, true)['data']));
        });
    }
}

