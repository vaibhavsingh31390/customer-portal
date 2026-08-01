<?php

namespace Tests\Feature;

use App\Models\ComplaintMessage;
use App\Models\CustomerComplaint;
use App\Models\User;
use Database\Seeders\CelintMasterSeeder;
use Database\Seeders\EngMasterSeeder;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['test.enabled' => true]);

        $this->seed([
            CelintMasterSeeder::class,
            EngMasterSeeder::class,
            TestUserSeeder::class,
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

    protected function validComplaintPayload(array $overrides = []): array
    {
        return array_merge([
            'P3_COMPL_DT' => '2026-08-01',
            'P3_MODULE' => 'HR',
            'P3_COMPL_TYPE' => 'Form',
            'P3_ERROR_TYPE' => 'UP',
            'P3_PROBLEM_DESC' => 'Test complaint description',
            'P3_COMPL_LEVEL' => 'L',
            'P3_CONTACT_MAIL_ID' => 'acme@example.com',
        ], $overrides);
    }

    public function test_client_can_create_complaint_without_posting_client_code(): void
    {
        $response = $this->actingAsPortalUser('C001')
            ->postJson(route('save.create.complaint'), $this->validComplaintPayload());

        $response->assertOk()
            ->assertJson(['type' => 1]);

        $complaint = CustomerComplaint::query()->latest('complaint_number')->first();

        $this->assertNotNull($complaint);
        $this->assertSame('C001', $complaint->client_code);
        $this->assertSame('PN', $complaint->status);
        $this->assertNull($complaint->assigned_to);

        $this->assertDatabaseHas('complaint_messages', [
            'complaint_number' => $complaint->complaint_number,
            'author_user_code' => 'C001',
        ]);
    }

    public function test_client_cannot_assign_complaint_to_another_client(): void
    {
        $response = $this->actingAsPortalUser('C001')
            ->postJson(route('save.create.complaint'), $this->validComplaintPayload([
                'P3_CUST_CD' => 'C002',
                'P3_STATUS_TYPE' => 'CM',
                'P8_ASSIGN_TO' => 'S001',
            ]));

        $response->assertOk()->assertJson(['type' => 1]);

        $complaint = CustomerComplaint::query()->latest('complaint_number')->first();

        $this->assertSame('C001', $complaint->client_code);
        $this->assertSame('PN', $complaint->status);
        $this->assertNull($complaint->assigned_to);
    }

    public function test_support_can_create_complaint_for_selected_client(): void
    {
        $response = $this->actingAsPortalUser('S001')
            ->postJson(route('save.create.complaint'), $this->validComplaintPayload([
                'P8_CUST_CD' => 'C002',
                'P3_STATUS_TYPE' => 'HL',
                'P8_ASSIGN_TO' => 'S002',
                'P8_TIME_TAKEN' => '2',
            ]));

        $response->assertOk()->assertJson(['type' => 1]);

        $complaint = CustomerComplaint::query()->latest('complaint_number')->first();

        $this->assertSame('C002', $complaint->client_code);
        $this->assertSame('HL', $complaint->status);
        $this->assertSame('S002', $complaint->assigned_to);
        $this->assertSame('2', $complaint->time_taken);

        $message = ComplaintMessage::query()
            ->where('complaint_number', $complaint->complaint_number)
            ->first();

        $this->assertSame('S001', $message->author_user_code);
        $this->assertSame('support', $message->author_role);
    }

    public function test_admin_can_create_complaint_for_selected_client(): void
    {
        $response = $this->actingAsPortalUser('A000')
            ->postJson(route('save.create.complaint'), $this->validComplaintPayload([
                'P8_CUST_CD' => 'C001',
                'P3_STATUS_TYPE' => 'PN',
            ]));

        $response->assertOk()->assertJson(['type' => 1]);

        $complaint = CustomerComplaint::query()->latest('complaint_number')->first();

        $this->assertSame('C001', $complaint->client_code);
        $this->assertSame('PN', $complaint->status);
    }

    public function test_staff_cannot_create_complaint_without_customer(): void
    {
        $response = $this->actingAsPortalUser('S001')
            ->postJson(route('save.create.complaint'), $this->validComplaintPayload());

        $response->assertOk()
            ->assertJson(['type' => 0]);

        $this->assertSame(0, CustomerComplaint::query()->count());
    }
}
