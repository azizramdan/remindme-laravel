<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Notifications\Reminder as NotificationsReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminder implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Reminder $reminder)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->reminder->user->notify(new NotificationsReminder($this->reminder));

        $this->reminder->update([
            'sent_at' => time(),
        ]);
    }

    public function uniqueId(): string
    {
        return $this->reminder->id;
    }
}
