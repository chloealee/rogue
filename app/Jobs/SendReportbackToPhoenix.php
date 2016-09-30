<?php

namespace Rogue\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Rogue\Services\Phoenix;
use Rogue\Models\Reportback;
use Log;

class SendReportbackToPhoenix extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $reportback;
    protected $transactionID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Reportback $reportback, $transactionID)
    {
        $this->reportback = $reportback;
        $this->transactionID = $transactionID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $phoenix = new Phoenix;

        $reportbackItem = $this->reportback->items()->orderBy('created_at', 'desc')->first();

        $body = [
            'uid' => $this->reportback->drupal_id,
            'nid' => $this->reportback->campaign_id,
            'quantity' => $this->reportback->quantity,
            'why_participated' => $this->reportback->why_participated,
            'file_url' => $reportbackItem->file_url,
            'caption' => $reportbackItem->caption,
            'source' => $reportbackItem->source,
        ];

        // Increment transaction ID.
        $this->transactionID++;

        $phoenix->postReportback($this->reportback->campaign_id, $body);

        // Log that the reportback has been sent to Phoenix.
        Log::info('Reportback request sent back to Phoenix. Transaction ID: ' . $this->transactionID);
    }
}
