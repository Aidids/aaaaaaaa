<?php

namespace App\Services;

use App\Models\User;
use App\Models\PermanentAddress;
use App\Models\CurrentAddress;
use App\Models\EmergencyContact;


class ProfileServices
{
    public function getProfile(int $id)
    {
        return User::find($id);
    }

    public function updateProfile(int $id, Array $data)
    {
        User::find($id)->update($data);

        return User::find($id);
    }

    public function getPermanentAddress(int $id)
    {
        return User::find($id)->permAddress;
    }

    public function updatePermanentAddress(int $id, Array $data)
    {
        PermanentAddress::updateOrCreate(
            ['user_id' => $id],
            $data
        );

        return User::find($id)->permAddress;
    }

    public function deletePermanentAddress(int $id): String
    {
        PermanentAddress::where('user_id', $id)->delete();

        return 'permanent address deleted';
    }

    public function getCurrentAddress(int $id)
    {
        return User::find($id)->currAddress;
    }

    public function updateCurrentAddress(int $id, Array $data): CurrentAddress
    {
        CurrentAddress::updateOrCreate(
            ['user_id' => $id],
            $data
        );

        return User::find($id)->currAddress;
    }

    public function deleteCurrentAddress(int $id): String
    {
        CurrentAddress::where('user_id', $id)->delete();

        return 'current address deleted';
    }

    public function getEmergencyContact(int $id)
    {
        return User::find($id)->emergency;
    }


    public function updateEmergencyContact(int $id, Array $data)
    {
        $userId = ['user_id' => $id];

        return EmergencyContact::updateOrCreate(
            ['id' => $data['id'] ?? null],
            array_merge($userId, $data)
        );
    }

    public function deleteEmergencyContact(int $userId, int $contactId): String
    {
        EmergencyContact::find($contactId)->delete();

        return 'Emergency contact deleted';
    }

    public function getAllEmployees($query)
    {
        if (is_null($query))
        {
            return User::paginate(12);
        }

        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate(12);

    }
}
