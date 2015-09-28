<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Redis;

pcntl_signal(SIGINT, function($signo) {
	echo "Got SIGINT\n";
	PcntlTest::$signo = $signo;
	Redis::set(PcntlTest::$signokey, PcntlTest::$signo);
	posix_kill(posix_getpid(), SIGTERM);
});

pcntl_signal(SIGTERM, function($signo) {
	echo "Got SIGTERM\n";
	PcntlTest::$signo = $signo;
	Redis::set(PcntlTest::$signokey, PcntlTest::$signo);
});


class PcntlTest extends Command
{

	const NO_SIGNAL = -1;
	public static $signo = self::NO_SIGNAL;
	public static $pid;
	public static $signokey;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pcntl:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Pcntl';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
	self::$pid = posix_getpid();
	self::$signokey = 'signo.'.self::$pid;
	Redis::set(self::$signokey, self::$signo);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (true) {
		pcntl_signal_dispatch();
		if (self::$signo > 0) {
			$this->handle_signal();
		}
		sleep(1);
	}
    }

	private function handle_signal() {
		$signo = Redis::get(self::$signokey);
		echo "Got signal ". $signo . "\n";
		if (self::$signo === SIGTERM) {
			Redis::del(self::$signokey);
			exit();
		}
		self::$signo = -1;
		Redis::set(self::$signokey, self::$signo);
	}
}
