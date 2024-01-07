<?php

namespace App\Console\Commands;

use App\Jobs\SendReminder as JobsSendReminder;
use App\Models\Reminder;
use Illuminate\Console\Command;

class SendReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminders = Reminder::query()
            ->with('user')
            ->whereNull('sent_at')
            ->where('remind_at', '<=', time())
            ->get();

        foreach ($reminders as $reminder) {
            JobsSendReminder::dispatch($reminder);
        }
    }
}
