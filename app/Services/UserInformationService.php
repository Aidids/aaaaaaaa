<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\FamilyDetails;
use App\Models\PersonalInformation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserInformationService extends Controller
{

    public function storeInformation(Array $data)
    {
        // If user P.I not exist before, create new
        // but if existed, update to the latest value
        $personalInformation = PersonalInformation::updateOrCreate(
            [
                'user_id' => $data['user_id'],
            ],
            $data,
        );

        if ($personalInformation) {
            return [
                'status' => 200,
                'message' => 'Personal information update Successful',
            ];
        }
        return [
            'status' => 500,
            'message' => 'Personal information update Failed!! (please contact admin)',
        ];
    }

    public function storeFamilyDetail(Array $data)
    {
        // This to prevent error, for below scenario
        // User update their family detail first before updating personal information
        $personalInformation = PersonalInformation::where('user_id', $data['user_id'])->first();
        if (!$personalInformation) {
            $data['personal_information_id'] = PersonalInformation::create(['user_id' => $data['user_id']])->id;
        }

        // Update spouse information first, before storing children information
        $data = $this->storeSpouseDetail($data);
        if($data === false) {
            //for file PDF type validation
            return [
                'status' => 403,
                'message' => 'Only PDF marriage certificate is allowed',
            ];

        }

        // Updating spouse information (P.I Table)
        $personalInformation = PersonalInformation::updateOrCreate(
            [
                'id' => $data['personal_information_id'],
                'user_id' => $data['user_id'],
            ],
            $data,
        );

        // Update children information (F.D Table)
        if (array_key_exists('childrens', $data))
        {
            $familyDetail = $this->storeChildrenDetails($data);

            if ($familyDetail)
            {
                return [
                    'status' => 200,
                    'message' => 'Family Detail update Successful',
                ];
            }
        }

        if ($personalInformation) {
            return [
                'status' => 200,
                'message' => 'Spouse Detail update Successful',
            ];
        }
        return [
            'status' => 500,
            'message' => 'Family Detail update fail (please contact admin)',
        ];
    }

    private function storeSpouseDetail(Array $data)
    {
        // if marital_status == single, reset spouse value to default
        if ($data['marital_status'] == 0) {
            $this->deleteMarriageCert($data['personal_information_id']);

            $data['spouse_name'] = NULL;
            $data['spouse_ic_no'] = NULL;
            $data['marriage_cert_path'] = NULL;
            $data['spouse_work'] = 0;

            return $data;
        }

        if (array_key_exists('marriage_cert', $data)) {
            if (! $data['marriage_cert']->isValid()) {
                return false; // if file not valid, return error message
            }

            if ($data['marriage_cert']->getClientOriginalExtension() !== 'pdf') {
                return false;
            }

            // Delete Previous attachment, before uploading the latest attachment
            $this->deleteMarriageCert($data['personal_information_id']);

            // added UniqueID() to prevent file overwriting
            $file = file_get_contents($data['marriage_cert']);
            $file_path = $data['personal_information_id']. '_' . uniqid() . '.' .$data['marriage_cert']->getClientOriginalExtension();

            Storage::disk('personal-attachment')->put($file_path, $file);

            $data['marriage_cert_path'] = $file_path;
            unset($data['marriage_cert']);
        }

        return $data;
    }

    private function deleteMarriageCert(Int $data): void
    {
        $attachment = PersonalInformation::find($data);

        if($attachment->marriage_cert_path !== NULL) {
            Storage::disk('personal-attachment')->delete($attachment->marriage_cert_path);
        }
    }

    private function storeChildrenDetails(Array $data)
    {
        // Validation if user delete child
        $this->compareChildData($data); // (compare request data with db data)

        foreach(json_decode($data['childrens']) as $index => $children) {
            $family = FamilyDetails::updateOrCreate(
                [
                    'id' => $children->id,
                    'user_id' => $data['user_id'],
                    'personal_information_id' => $data['personal_information_id'],
                ],
                [
                    'child_name' => $children->child_name,
                    'child_ic_no' => $children->child_ic,
                ]
            );


            if ($data['birth_certs'][$index]) { // check if request send birth_cert_attachments
                if ($data['birth_certs'][$index]->isValid() && $data['birth_certs'][$index]->getClientOriginalExtension() === 'pdf') {
                    // Delete Previous Birth Certificate Attachment
                    $this->deleteChildrenBirthCert($family->id);

                    // added UniqueID() to prevent file overwriting
                    $file = file_get_contents($data['birth_certs'][$index]);
                    $file_path = $family->id . '_' .$data['personal_information_id'] . '_' . uniqid() . '.' . $data['birth_certs'][$index]->getClientOriginalExtension();
                    Storage::disk('personal-attachment')->put($file_path, $file);

                    $family->update(['child_cert_path' => $file_path]);
                }
            }
        }

        return count(json_decode($data['childrens']));
    }

    private function compareChildData(Array $data): void
    {
        $childID = [];
        // assigned child id from request to an array for comparison
        foreach (json_decode($data['childrens']) as $children) {
            array_push($childID, $children->id);
        }

        $removedChildID = FamilyDetails::whereNotIn('id', $childID)->get();
        foreach ($removedChildID as $removeBirthCert) {
            $this->deleteChildrenBirthCert($removeBirthCert->id);
        }

        FamilyDetails::whereNotIn('id', $childID)->delete();
    }

    private function deleteChildrenBirthCert(Int $data): void
    {
        $attachment = FamilyDetails::find($data);

        if($attachment->child_cert_path !== NULL) {
            Storage::disk('personal-attachment')->delete($attachment->child_cert_path);
        }
    }
}
