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

class SendGroupNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group;
    protected $item;

    /**
     * SendGroupNotification constructor.
     * @param $group
     * @param $item
     */
    public function __construct($group, $item)
    {
        $this->group = $group;
        $this->item = $item;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->group->members as $member) {
            // Send Notification
            $notification = [
                'title'   => 'Item Add',
                'type'    => 'group_item_add',
                'subject' => 'Item add in the '.$this->group->title.' group',
                'message' => 'Item added '.$this->item->name.'.',
                'url'     => '/',
                'action'  => 'View Item',
            ];
            $member->notify(new GeneralNotification($notification));

            $data = [
                'group_id' => $this->group->id,
                'group_name' => $this->group->title,
                'member_name'   => $member->name,
                'item_name'   => $this->item->name,
                'item_description' => $this->item->description,
                'creator_name' => $this->item->user()->name,
                'view'     => 'emails.add-item',
            ];

            // Send Email
            Mail::to($member->email)->send(new GeneralMail($data));
        }

        $this->item->update(['is_notify' => 1]);
    }
}
