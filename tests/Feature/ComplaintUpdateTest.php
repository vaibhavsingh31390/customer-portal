<?php

namespace Tests\Feature;

use App\Models\CustomerComplaint;
use App\Models\User;
use Database\Seeders\CelintMasterSeeder;
use Database\Seeders\EngMasterSeeder;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerComplaint $complaint;

    protected function setUp(): void
    {
        parent::setUp();

        config(['test.enabled' => true]);

        $this->seed([
            CelintMasterSeeder::class,
            EngMasterSeeder::class,
            TestUserSeeder::class,
        ]);

        $this->complaint = CustomerComplaint::query()->create([
            'id' => 'COMP-TEST-001',
            'complaint_number' => 'CP'.date('Ym').'9999',
            'complaint_date' => '2026-08-01',
            'client_code' => 'C001',
            'module' => 'HR',
            'complaint_type' => 'Form',
            'error_type' => 'UP',
            'problem_description' => 'Original description',
            'priority' => 'L',
            'status' => 'PN',
            'contact_name' => 'Client User',
            'contact_email' => 'acme@example.com',
            'assigned_to' => 'S001',
        ]);
    }

    protected function actingAsPortalUser(string $userCode): self
    {
        $user = User::query()->findOrFail($userCode);

        return $this->withSession([
            'user_token' => encrypt($user->user_code),
            'user' => $user,
            'name' => 'Test User',
        ]);
    }

    protected function editPayload(array $overrides = []): array
    {
        return array_merge([
            'P3_COMPL_DT' => '2026-08-01',
            'P3_MODULE' => 'Finance',
            'P3_COMPL_TYPE' => 'Report',
            'P3_ERROR_TYPE' => 'SP',
            'P3_PROBLEM_DESC' => 'Updated description',
            'P3_COMPL_LEVEL' => 'M',
            'P3_STATUS_TYPE' => 'HL',
            'P3_USER_NAME' => 'Client User',
            'P3_CONTACT_MAIL_ID' => 'acme@example.com',
            'P8_TIME_TAKEN' => '3',
            'P8_ASSIGN_TO' => 'S002',
            'P8_CHANGE_DONE_BY' => 'S002',
            'P3_MAWAI_REMARKS' => 'Working on it',
            'P8_REASON' => 'Config issue',
            'P8_ACTION' => 'Applied fix',
        ], $overrides);
    }

    protected function encodedComplaintId(): string
    {
        return base64_encode($this->complaint->complaint_number);
    }

    public function test_support_can_update_complaint_using_customer_select_only(): void
    {
        $response = $this->actingAsPortalUser('S001')
            ->postJson(route('save.edit.complaint', ['id' => $this->encodedComplaintId()]), $this->editPayload([
                'P8_CUST_CD' => 'C001',
            ]));

        $response->assertOk()->assertJson(['type' => 1]);

        $this->complaint->refresh();

        $this->assertSame('C001', $this->complaint->client_code);
        $this->assertSame('HL', $this->complaint->status);
        $this->assertSame('Updated description', $this->complaint->problem_description);
        $this->assertSame('S002', $this->complaint->assigned_to);
    }

    public function test_support_update_preserves_client_code_when_customer_fields_omitted(): void
    {
        $response = $this->actingAsPortalUser('S001')
            ->postJson(route('save.edit.complaint', ['id' => $this->encodedComplaintId()]), $this->editPayload());

        $response->assertOk()->assertJson(['type' => 1]);

        $this->complaint->refresh();

        $this->assertSame('C001', $this->complaint->client_code);
    }

    public function test_support_can_reassign_complaint_to_another_client(): void
    {
        $response = $this->actingAsPortalUser('S001')
            ->postJson(route('save.edit.complaint', ['id' => $this->encodedComplaintId()]), $this->editPayload([
                'P8_CUST_CD' => 'C002',
            ]));

        $response->assertOk()->assertJson(['type' => 1]);

        $this->complaint->refresh();

        $this->assertSame('C002', $this->complaint->client_code);
    }

    public function test_support_cannot_mark_complaint_complete_via_form(): void
    {
        $response = $this->actingAsPortalUser('S001')
            ->postJson(route('save.edit.complaint', ['id' => $this->encodedComplaintId()]), $this->editPayload([
                'P8_CUST_CD' => 'C001',
                'P3_STATUS_TYPE' => 'CM',
            ]));

        $response->assertOk()->assertJson(['type' => 0]);

        $this->complaint->refresh();

        $this->assertSame('PN', $this->complaint->status);
    }

    public function test_admin_can_mark_complaint_complete_via_form(): void
    {
        $response = $this->actingAsPortalUser('A000')
            ->postJson(route('save.edit.complaint', ['id' => $this->encodedComplaintId()]), $this->editPayload([
                'P8_CUST_CD' => 'C001',
                'P3_STATUS_TYPE' => 'CM',
            ]));

        $response->assertOk()->assertJson(['type' => 1]);

        $this->complaint->refresh();

        $this->assertSame('CM', $this->complaint->status);
        $this->assertNotNull($this->complaint->closed_date);
    }

    public function test_client_cannot_update_complaint_via_form_endpoint(): void
    {
        $response = $this->actingAsPortalUser('C001')
            ->postJson(route('save.edit.complaint', ['id' => $this->encodedComplaintId()]), $this->editPayload());

        $response->assertOk()->assertJson(['type' => 0]);

        $this->complaint->refresh();

        $this->assertSame('Original description', $this->complaint->problem_description);
    }

    public function test_client_can_post_thread_reply(): void
    {
        $response = $this->actingAsPortalUser('C001')
            ->postJson(route('complaint.messages.store', ['id' => $this->encodedComplaintId()]), [
                'body' => 'Any update on this?',
            ]);

        $response->assertOk()->assertJson(['type' => 1]);

        $this->assertDatabaseHas('complaint_messages', [
            'complaint_number' => $this->complaint->complaint_number,
            'author_user_code' => 'C001',
            'body' => 'Any update on this?',
        ]);
    }

    public function test_client_must_rate_when_closing_as_complete(): void
    {
        $response = $this->actingAsPortalUser('C001')
            ->postJson(route('complaint.close', ['id' => $this->encodedComplaintId()]), [
                'status' => 'CM',
            ]);

        $response->assertOk()->assertJson(['type' => 0]);

        $this->complaint->refresh();

        $this->assertSame('PN', $this->complaint->status);
    }

    public function test_client_can_close_complaint_with_rating(): void
    {
        $response = $this->actingAsPortalUser('C001')
            ->postJson(route('complaint.close', ['id' => $this->encodedComplaintId()]), [
                'status' => 'CM',
                'rating' => 4,
                'body' => 'Resolved, thanks.',
            ]);

        $response->assertOk()->assertJson(['type' => 1]);

        $this->complaint->refresh();

        $this->assertSame('CM', $this->complaint->status);
        $this->assertEquals(4, $this->complaint->rating);
    }

    public function test_client_cannot_access_another_clients_complaint(): void
    {
        $response = $this->actingAsPortalUser('C002')
            ->get(route('show.edit.complaint', ['id' => $this->encodedComplaintId()]));

        $response->assertNotFound();
    }

    public function test_malicious_sort_column_is_ignored(): void
    {
        $response = $this->actingAsPortalUser('S001')
            ->postJson(route('complaint.list'), [
                'sorting' => 'complaint_number;drop table customer_complaints',
                'order' => 'DESC',
                'per_page' => 10,
            ]);

        $response->assertOk()->assertJson(['status' => 1]);
    }
}
