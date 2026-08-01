<?php

namespace Tests\Unit;

use App\Models\CustomerComplaint;
use App\Support\ComplaintAnalytics;
use App\Support\ComplaintStatus;
use Database\Seeders\CelintMasterSeeder;
use Database\Seeders\EngMasterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintStatusFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CelintMasterSeeder::class,
            EngMasterSeeder::class,
        ]);
    }

    public function test_status_filter_limits_complaint_query(): void
    {
        CustomerComplaint::query()->create([
            'id' => 'COMP-F-001',
            'complaint_number' => 'CP'.date('Ym').'8001',
            'complaint_date' => now()->toDateString(),
            'client_code' => 'C001',
            'status' => 'PN',
        ]);
        CustomerComplaint::query()->create([
            'id' => 'COMP-F-002',
            'complaint_number' => 'CP'.date('Ym').'8002',
            'complaint_date' => now()->toDateString(),
            'client_code' => 'C001',
            'status' => 'CM',
        ]);

        $openCount = ComplaintStatus::applyFilter(CustomerComplaint::query(), 'open')->count();
        $closedCount = ComplaintStatus::applyFilter(CustomerComplaint::query(), 'closed')->count();

        $this->assertSame(1, $openCount);
        $this->assertSame(1, $closedCount);
    }

    public function test_monthly_status_counts_include_breakdown(): void
    {
        CustomerComplaint::query()->create([
            'id' => 'COMP-F-003',
            'complaint_number' => 'CP'.date('Ym').'8003',
            'complaint_date' => now()->toDateString(),
            'client_code' => 'C001',
            'status' => 'HL',
        ]);

        $counts = ComplaintAnalytics::monthlyStatusCounts('C001');
        $chart = ComplaintAnalytics::chartPayload($counts);

        $this->assertSame(1, $counts['HL']);
        $this->assertCount(5, $chart['labels']);
        $this->assertCount(5, $chart['data']);
    }
}
