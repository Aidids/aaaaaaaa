<?php
//
//namespace Tests\Feature;
//
//use App\Enums\LeaveRequestStatus;
//use App\Models\TravelAuthorization;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Tests\ArrangeData;
//use Tests\TestCase;
//
//class TravelAuthorizationFeatureTest extends TestCase
//{
//    use ArrangeData, RefreshDatabase;
//
//    public function test_new_user_browsing_travel_authorization_index_screen()
//    {
//        $response = $this->actingAs($this->user)->getJson('api/travel-authorization');
//        $response->assertJson([
//            'data' => []
//        ]);
//    }
//
//    public function test_user_cannot_see_others_travel_authorization_history()
//    {
//        $this->createEform(
//            user: $this->randomUser,
//            model: new TravelAuthorization(),
//            type: TravelAuthorization::class
//        );
//
//        $this->test_new_user_browsing_travel_authorization_index_screen();
//    }
//
//    public function test_user_apply_travel_authorization_eform()
//    {
//        $data = $this->data();
//        $response = $this->actingAs($this->user)->postJson('api/travel-authorization/apply', $data);
//        $response->assertJsonFragment($response->json('data'));
//
//        return $response;
//    }
//
//    public function test_approver_receive_eform_from_users()
//    {
//        $eForm = $this->test_user_apply_travel_authorization_eform();
//
//        $response = $this->actingAs($this->approver1)->getJson('api/travel-authorization/approver');
//        $response->assertJsonFragment($eForm->json('data'));
//
//        $response = $this->actingAs($this->approver2)->getJson('api/travel-authorization/approver');
//        $response->assertJsonMissing($eForm->json('data'));
//
//        return $eForm;
//    }
//
//
//
//    private function data()
//    {
//        return [
//            'user_id' => $this->user->id,
//            'department_id' => $this->user->department_id,
//            'travel_purpose' => fake()->randomElement(array(0,1)),
//            'main_office' => fake()->randomElement(array(0,1)),
//            'reimbursement' => fake()->randomElement(array(0,1)),
//            'project_name' => fake()->word(),
//            'project_location' => fake()->word,
//            'location' => '[{"to": "Miri", "from": "Kuala Lumpur", "end_date": "2023-10-07", "start_date": "2023-10-01", "flight_type": 1, "accomodation": 1},
//            {"to": "Kemaman", "from": "Miri", "end_date": "2023-10-20", "start_date": "2023-10-07", "flight_type": 2, "accomodation": 1},
//            {"to": "Kuala Lumpur", "from": "Miri", "end_date": "2023-10-31", "start_date": "2023-10-20", "flight_type": 1, "accomodation": 1}]',
//            'purpose' => fake()->bs(),
//
//            'first_approver_id' => $this->approver1->id,
//            'first_approver_status' => LeaveRequestStatus::pending->value,
//
//            'second_approver_id' => $this->approver2->id,
//            'second_approver_status' => LeaveRequestStatus::pending->value,
//        ];
//    }
//}
