<?php

namespace App\Http\Controllers;

use App\Constants\HostelConstants;
use App\DataTransferObjects\DashboardAsideDto;
use App\DataTransferObjects\DashboardReminderDto;
use App\DataTransferObjects\ResidenceDto;
use App\DataTransferObjects\ResidenceResponseDto;
use App\DataTransferObjects\StudentProfileDto;
use App\Http\Helpers\HomeHelpers;
use App\Traits\Utils;
use App\Models\RoomRange;
use App\Traits\HttpResponses;
use App\Http\Requests\UserRequest;
use App\Http\Services\HomeService;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{

    use HttpResponses;
    use Utils;

    public function __construct(private HomeService $homeService, private HomeHelpers $homeHelpers,)
    {
    }

    public function dashboardReminder($studentID)
    {
        $student = $this->homeService->getDashboardReminder($studentID);
        $dto = new DashboardReminderDto($student);
        return $this->sendData($dto->data());
    }

    public function dashboardAside($studentID)
    {
        //get the accommodation fee
        $hostelFees = $this->hostelFees($studentID);
        //check-in-out dates
        $checkInOut = $this->checkInOut($studentID);
        //fetch the login time stamp
        $timestamp = $this->homeService->getTimestamps($studentID);
        $previousTimeStamp = $timestamp->previous_stamp ? $timestamp->previous_stamp : null;
        $dto = new DashboardAsideDto($hostelFees, $checkInOut, $previousTimeStamp);

        return $this->sendData($dto->data());
    }

    public function profile($studentID)
    {
        $profile = $this->homeService->getStudentProfile($studentID);
        $dto = new StudentProfileDto($profile);
        return $this->sendData($dto->data());
    }

    public function residence($studentID)
    {
        $currentRes = $this->homeService->getStudentResidence($studentID);
        $previousRes = $this->homeService->getStudentOldResidence($studentID);

        $__gender = $this->gender($studentID);

        if (count($previousRes))
            $__previousRes = $this->homeHelpers->formatPreviousResData($studentID, $__gender, $previousRes);
        else
            $__previousRes = null;

        //if the student has been allocated a room
        switch ($currentRes) {
            case true:
                $__currentRes = $this->homeHelpers->res_data($studentID, $__gender, true, $currentRes);
                $dto = new ResidenceResponseDto($__currentRes, $__previousRes);
                return $this->sendData($dto->data());
            case false:
                $dto = new ResidenceResponseDto(null, $__previousRes);
                return $this->sendData($dto->data());
        }
    }

    //function to change password
    public function verifyCurrentPassword(UserRequest $request)
    {
        $request->validated($request->all());

        $student = $this->homeService->getUserPassword($request->studentID);
        //if password is incorrect
        if (!Hash::check($request->password, $student->password)) return $this->sendError('Incorrect password', 401);

        return $this->sendResponse('Password verified successfully');
    }

    //function to change password
    public function updatePassword(UserRequest $request)
    {
        $request->validated($request->all());

        //update with new password
        $this->homeService->updateUserPassword($request->studentID, Hash::make($request->password));

        return $this->sendResponse('Password updated successfully');
    }
}
