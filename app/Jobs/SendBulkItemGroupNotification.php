<?php

namespace App\Jobs;

use App\Mail\GeneralMail;
use App\Models\Group;
use App\Notifications\GeneralNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBulkItemGroupNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group;
    protected $creator;

    /**
     * SendGroupNotification constructor.
     * @param $group
     * @param $creator
     */
    public function __construct($group, $creator)
    {
        $this->group = $group;
        $this->creator = $creator;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->group->members as $member) {
            // Send Notification
            $notification = [
                'title'   => 'Bulk Item Add',
                'type'    => 'bulk_group_item_add',
                'subject' => 'Multiple new items added in the '.$this->group->title.' group',
                'message' => $this->creator->name.' has been added multiple new items '.$this->group->title.' group.',
                'url'     => '/',
                'action'  => 'View Items',
            ];
            $member->notify(new GeneralNotification($notification));

            $data = [
                'group_id' => $this->group->id,
                'group_name' => $this->group->title,
                'member_name'   => $member->name,
                'creator_name' => $this->creator->name,
                'view'     => 'emails.add-bulk-item',
            ];

            // Send Email
            Mail::to($member->email)->send(new GeneralMail($data));
        }
    }
}
