<?php

namespace Tests\Feature;

use App\Enums\CompassionateType;
use App\Enums\LeaveRequestStatus;
use App\Http\Resources\LeaveRequestResource;
use App\Http\Resources\LeaveTypeResource;
use App\Models\Approver;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use LdapRecord\Laravel\Tests\Feature\CreatesTestUsers;
use Tests\TestCase;

class LeaveRequestFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        LeaveRequest::unsetEventDispatcher();
        parent::setUp();
        $this->user = $this->createUser();
        $this->admin = $this->createUser(is_admin: true);
        Artisan::call('offshore:create');
    }

    public function test_leave_request_index_page_unauthorized_for_other_users()
    {
        $leaveBalance = LeaveBalance::factory()->create();

        $response = $this->actingAs($this->user)->get('leave-request/'.$leaveBalance->user_id);
        $response->assertUnauthorized();
    }

    public function test_leave_request_index_page_able_to_get_all_leave()
    {
        $leaveBalance = LeaveBalance::factory()->create();

        $response = $this->get('leave-request/'.$leaveBalance->user_id);
        $response->assertStatus(302);

        $response = $this->actingAs($leaveBalance->user)->getJson('/api/leave-request/'.$leaveBalance->user_id);
        $response->assertJson([
            'data' => array([
                'id' => $leaveBalance->id,
                'name' => $leaveBalance->leave->name,
                'user_id' => $leaveBalance->user_id,
                'leave_type_id' => $leaveBalance->leave_type_id,
                'balance' => $leaveBalance->balance,
            ])
        ]);
    }

    public function test_editing_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->create([
            'first_approver_id' => $this->admin->id
        ]);

        $response = $this->actingAs($leaveRequest->user)->postJson('/api/leave-request/'.$leaveRequest->user->id,
            $this->createLeaveRequestData($leaveRequest->leave_balance_id, $this->admin->id)
        );
        $response->assertCreated();
    }

    public function test_cancel_not_calculated_approved_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->create([
            'user_id' => $this->user->id,
            'overall_status' => LeaveRequestStatus::approved->value,
        ]);

        $data = ['id' => $leaveRequest->id];

        $response = $this->actingAs($this->user)->postJson('/api/leave-request/'.$this->user->id.'/cancel', $data);

        $response->assertOk();
    }

    public function test_cancel_calculated_approved_leave_request()
    {
        $leaveBalance = LeaveBalance::where(['user_id' => $this->user->id])->first();

        if (is_null($leaveBalance)) {
            $leaveBalance = LeaveBalance::factory()->create([
                'user_id' => $this->user->id,
            ]);
        }

        $response = $this->actingAs($this->admin)->postJson('/api/administration/update-annual-leave',
            [
                'user_id' => $this->user->id,
                'leave_type_id' => $leaveBalance->leave_type_id,
                'pic_id' => $this->admin->id,
                'added_qty' => 10,
                'remark' => 'test',
            ]);

        $response->assertOk();

        $leaveRequest = LeaveRequest::factory()->create([
            'leave_balance_id' => $leaveBalance->id,
            'user_id' => $this->user->id,
            'first_approver_id' => $this->admin->id,
            'overall_status' => LeaveRequestStatus::approved->value,
        ]);

        $this->artisan('leave:deduction');
        //run artisan console to simulate 12 a.m. task scheduler action
        $leaveBalance = LeaveBalance::find($leaveRequest->leave_balance_id); //find deducted leave balance

        // cancel user leave, and add balance if automated calculation has executed
        $data = ['id' => $leaveRequest->id];
        $response = $this->actingAs($this->user)->postJson('/api/leave-request/'.$this->user->id.'/cancel', $data);
        $response->assertOk();

        // check if the user balance has been re-added after cancelation
        $response = $this->actingAs($this->user)->getJson('/api/leave-balance/'.$this->user->id);
        $response->assertJsonFragment([
            'taken' => $leaveBalance->taken - $leaveRequest->duration,
            'balance' => $leaveBalance->balance + $leaveRequest->duration,
        ]);
    }

    public function test_apply_compassionate_leave_request()
    {
        // create new leave type and balance record for compassionate type
        $this->artisan('compassionate:create');

        $compassionateLeaveType = LeaveType::getCompassionateLeaveType();
        $leaveBalance = LeaveBalance::where([
            'user_id' => $this->user->id, 'leave_type_id' => $compassionateLeaveType->id
        ])->first();

        $data = [
            'leave_balance_id' => $leaveBalance->id,
            'start_date' => '2023-08-31',
            'start_date_type' => 'full day',
            'end_date' => '2023-09-05',
            'end_date_type' => 'full day',
            'duration' => 3,
            'compassionate_type' => CompassionateType::Death->value,
            'calculated' => true,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/compassionate-leave/'.$this->user->id . '/', $data);
        $response->assertCreated();
    }

    private function createUser(bool $is_admin = false): User
    {
        return User::factory()->create([
            'is_admin' => $is_admin,
            'joining_date' => '2020-01-01',
        ]);
    }

    private function createLeaveRequestData(int $id, int $first_approver_id): array
    {
        return [
            'leave_balance_id' => $id,
            'start_date' => '2020-01-01',
            'start_date_type' => 'full day',
            'end_date' => '2020-01-01',
            'end_date_type' => 'morning',
            'duration' => 1,
            'first_approver_id' => $first_approver_id,
            /*TODO: Find a way to test document upload without uploading to actual storage*/
            /*'file' => UploadedFile::fake()->create('fakePdf.pdf', '5555120', 'pdf')*/
        ];
    }
}
