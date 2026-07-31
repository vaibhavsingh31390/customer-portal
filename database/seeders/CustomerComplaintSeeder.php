<?php

namespace Database\Seeders;

use App\Support\SqlHelper;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::parse(
            SqlHelper::selectOne('SELECT CURDATE() AS today')?->today ?? now()->toDateString()
        );

        $complaints = [
            [
                'id' => 'COMP-001',
                'complaint_number' => 'CP'.$today->format('Ym').'0001',
                'complaint_date' => $this->complaintDateInMonth($today, 2),
                'client_code' => 'C001',
                'module' => 'Sales',
                'complaint_type' => 'Form',
                'error_type' => 'SP',
                'problem_description' => 'Sales order form is not saving discount values.',
                'priority' => 'M',
                'status' => 'PN',
                'contact_name' => 'John Doe',
                'contact_email' => 'john@acme.com',
                'assigned_to' => 'S001',
            ],
            [
                'id' => 'COMP-002',
                'complaint_number' => 'CP'.$today->format('Ym').'0002',
                'complaint_date' => $this->complaintDateInMonth($today, 1),
                'client_code' => 'C001',
                'module' => 'Inventory',
                'complaint_type' => 'Report',
                'error_type' => 'DP',
                'problem_description' => 'Stock report shows incorrect closing balance.',
                'priority' => 'C',
                'status' => 'PN',
                'contact_name' => 'Jane Smith',
                'contact_email' => 'jane@acme.com',
                'assigned_to' => 'S002',
            ],
            [
                'id' => 'COMP-003',
                'complaint_number' => 'CP'.$today->format('Ym').'0003',
                'complaint_date' => $today->toDateString(),
                'client_code' => 'C002',
                'module' => 'Finance',
                'complaint_type' => 'Tables',
                'error_type' => 'UP',
                'problem_description' => 'Unable to post journal entries for branch transfers.',
                'priority' => 'M',
                'status' => 'PN',
                'contact_name' => 'Ravi Mehta',
                'contact_email' => 'ravi@bright.com',
                'assigned_to' => 'S001',
            ],
            [
                'id' => 'COMP-004',
                'complaint_number' => 'CP'.$today->format('Ym').'0004',
                'complaint_date' => $today->toDateString(),
                'client_code' => 'C002',
                'module' => 'Purchase',
                'complaint_type' => 'Form',
                'error_type' => 'NR',
                'problem_description' => 'Need approval workflow on purchase requisitions.',
                'priority' => 'L',
                'status' => 'CM',
                'contact_name' => 'Sneha Rao',
                'contact_email' => 'sneha@bright.com',
                'assigned_to' => 'S003',
                'closed_date' => $today->toDateString(),
            ],
            [
                'id' => 'COMP-005',
                'complaint_number' => 'CP'.$today->format('Ym').'0005',
                'complaint_date' => $today->toDateString(),
                'client_code' => 'C003',
                'module' => 'Production',
                'complaint_type' => 'Graph',
                'error_type' => 'OT',
                'problem_description' => 'Production dashboard chart fails to load for last month.',
                'priority' => 'M',
                'status' => 'PN',
                'contact_name' => 'Vikram Singh',
                'contact_email' => 'vikram@delta.com',
                'assigned_to' => 'S002',
            ],
            [
                'id' => 'COMP-006',
                'complaint_number' => 'CP'.$today->format('Ym').'0006',
                'complaint_date' => $today->copy()->subMonth()->toDateString(),
                'client_code' => 'C003',
                'module' => 'HR',
                'complaint_type' => 'Views',
                'error_type' => 'SP',
                'problem_description' => 'Employee attendance view is outdated.',
                'priority' => 'L',
                'status' => 'HL',
                'contact_name' => 'Anita Desai',
                'contact_email' => 'anita@delta.com',
                'assigned_to' => 'S003',
            ],
        ];

        foreach ($complaints as $complaint) {
            DB::table('customer_complaints')->updateOrInsert(
                ['id' => $complaint['id']],
                $complaint
            );
        }
    }

    private function complaintDateInMonth(Carbon $today, int $daysAgo): string
    {
        $candidate = $today->copy()->subDays($daysAgo);

        if ($candidate->format('Y-m') !== $today->format('Y-m')) {
            return $today->copy()->startOfMonth()->toDateString();
        }

        return $candidate->toDateString();
    }
}
