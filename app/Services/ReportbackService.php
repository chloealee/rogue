<?php

namespace Rogue\Services;

use Rogue\Models\Reportback;
use Rogue\Repositories\ReportbackRepository;
use Rogue\Jobs\SendReportbackToPhoenix;

class ReportbackService
{
    /*
     * Instance of \Rogue\Repositories\ReportbackRepository
     */
    protected $reportbackRepository;

    public function __construct(ReportbackRepository $reportbackRepository)
    {
        $this->reportbackRepository = $reportbackRepository;
    }

    /*
     * Handles all the logic around creating a reportback.
     *
     * @param array $data
     * @return \Rogue\Models\Reportback $reportback.
     */
    public function create($data, $transactionID)
    {
        $reportback = $this->reportbackRepository->create($data);

        // POST reportback back to Phoenix.
        // If request fails, record in failed_jobs table.
        dispatch(new SendReportbackToPhoenix($reportback, $transactionID));

        return $reportback;
    }

    /*
     * Handles all the business logic around updating a reportback.
     *
     * @param \Rogue\Models\Reportback $reportback
     * @param array $data
     *
     * @return \Rogue\Models\Reportback $reportback.
     */
    public function update($reportback, $data, $transactionID)
    {
        $reportback = $this->reportbackRepository->update($reportback, $data);

        // POST reportback update back to Phoenix.
        // If request fails, record in failed_jobs table.
        dispatch(new SendReportbackToPhoenix($reportback, $transactionID));

        return $reportback;
    }

    /*
     * Check if a reportback already exists for a given user,
     * on a specific campaign, and campaign run.
     *
     * @param string|int $campaignId
     * @param string|int $campaignRunId
     * @param string|int $userId
     * @param string $type
     *
     * @return \Rogue\Models\Reportback|null
     */
    public function getReportback($campaignId, $campaignRunId, $userId, $type)
    {
        $reportback = $this->reportbackRepository->getReportbackByUser($campaignId, $campaignRunId, $userId, $type);

        return $reportback ? $reportback : null;
    }

    /*
     * Handles all business logic around update a reportbackitem(s).
     *
     * @param array $data
     *
     * @return
     */
    public function updateReportbackItems($data)
    {
        $items = $this->reportbackRepository->updateReportbackItems($data);

        return $items;
    }
}


