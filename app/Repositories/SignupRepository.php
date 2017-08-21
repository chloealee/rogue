<?php

namespace Rogue\Repositories;

use Rogue\Models\Signup;
use DoSomething\Gateway\Blink;

class SignupRepository
{
    /**
     * Blink API client.
     *
     * @var \DoSomething\Gateway\Blink
     */
    protected $blink;

    /**
     * Create a SignupRepository.
     *
     * @param Blink $blink
     */
    public function __construct(Blink $blink)
    {
        $this->blink = $blink;
    }

    /**
     * Create a signup.
     *
     * @param  array $data
     * @return \Rogue\Models\Signup|null
     */
    public function create($data)
    {
        // Create the signup
        $signup = new Signup;

        $signup->northstar_id = $data['northstar_id'];
        $signup->campaign_id = $data['campaign_id'];
        $signup->campaign_run_id = $data['campaign_run_id'];
        $signup->quantity = isset($data['quantity']) ? $data['quantity'] : null;
        $signup->quantity_pending = isset($data['quantity_pending']) ? $data['quantity_pending'] : null;
        $signup->why_participated = isset($data['why_participated']) ? $data['why_participated'] : null;
        $signup->source = isset($data['source']) ? $data['source'] : null;

        if (isset($data['created_at'])) {
            // Manually set created and updated times for the signup
            $signup->created_at = $data['created_at'];
            $signup->updated_at = isset($data['updated_at']) ? $data['updated_at'] : $data['created_at'];
            $signup->save(['timestamps' => false]);

            // Manually update the signup event timestamp.
            $event = $signup->events->first();
            $event->created_at = $data['created_at'];
            $event->updated_at = isset($data['updated_at']) ? $data['updated_at'] : $data['created_at'];
            $event->save(['timestamps' => false]);
        } else {
            $signup->save();
        }

        // Save the new signup in Customer.io, via Blink.
        if (config('features.blink')) {
            $payload = $signup->toBlinkPayload();
            $this->blink->userSignup($payload);
        }

        return $signup;
    }

    /**
     * Get a signup
     *
     * @param  string $northstarId
     * @param  int $campaignId
     * @param  int $campaignRunId
     * @return \Rogue\Models\Signup|null
     */
    public function get($northstarId, $campaignId, $campaignRunId)
    {
        $signup = Signup::where([
            'northstar_id' => $northstarId,
            'campaign_id' => $campaignId,
            'campaign_run_id' => $campaignRunId,
        ])->first();

        return $signup;
    }
}
