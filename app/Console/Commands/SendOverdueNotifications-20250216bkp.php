<?php

namespace App\Console\Commands;

use App\Models\Entries;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendOverdueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-overdue-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $expiringItems = Entries::where('is_reserved', 2)->where('due_date', $today)->with(['book', 'reserver'])->get();

        foreach ($expiringItems as $item) {
            $user = $item->reserver;
            $book = $item->book;

            if (!$user || !$book) {
                Log::warning('Missing user or book for item ID: ' . $item->id);
                continue;
            }

            $notification = [
                'title' => 'Book Reservation Expiring Today',
                'type' => 'book_expiring_today',
                'subject' => 'Book Reservation Expiring Today',
                'message' => "Your reservation for the book {$book->name} is expiring today. Please take necessary action.",
                'url' => route('show-group', $book->group->id),
                'action' => 'View Item',
            ];

            try {
                $user->notify(new GeneralNotification($notification));
            } catch (\Exception $e) {
                Log::error('Failed to send notification for item ID: ' . $item->id . ' - Error: ' . $e->getMessage());
            }
        }
    }
}
