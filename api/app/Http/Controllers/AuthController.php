<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Utils;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\LoginTimestamps;
use App\Http\Requests\UserRequest;
use App\Http\Services\AuthService;
use App\Constants\FacultyConstants;
use App\Http\Requests\ValidateUser;
use App\DataTransferObjects\AuthDto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\AccountExistsException;
use App\Constants\SearchConstants;
use App\Exceptions\StudentNotFoundException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UnauthorizedAccessException;

class AuthController extends Controller
{

    use HttpResponses;
    use Utils;

    public function __construct(private AuthService $authService)
    {
    }

    public function validateCredentials(ValidateUser $request)
    {
        $request->validated($request->all());

        //verify student using studentID and nationalID
        $student = $this->authService->verifyNationalID($request->student_id, $request->nationalID);

        if (!$student) throw new StudentNotFoundException();


        if ($request->dob) {
            //verify student with studentID and DOB
            $student = $this->authService->verifyDOB($request->student_id, $request->dob);

            if (!$student)  throw new StudentNotFoundException();
        }

        return $this->sendResponse('Credentials verified successfully');
    }

    public function register(UserRequest $request)
    {
        $request->validated($request->all());

        //check if the student is allowed to create an account
        //1. If you a commerce student boot out
        $studentFaculty = $this->facultyID($request->studentID);

        if ($studentFaculty === FacultyConstants::COMMERCE_FACULTY['faculty_id']) {
            throw new UnauthorizedAccessException();
        } else if ($this->authService->accountExists($request->studentID)) {
            throw new AccountExistsException();
        }

        // create user 
        $user = $this->authService->createUser($request->studentID, $request->password);
        // update the login in timestamp
        $this->authService->logTimestamp($request->studentID);

        $userFullName = $this->authService->getStudentFullName($request->studentID);
        $token = $user->createToken('API token of ' . $userFullName)->plainTextToken;

        $dto = new AuthDto($request->studentID, $userFullName, $token);

        return $this->sendData($dto->data(), 201);
    }

    public function login(UserRequest $request)
    {
        $request->validated($request->all());

        $verify = [
            'student_id' => $request->studentID,
            'password' => $request->password
        ];

        if (!Auth::attempt($verify)) throw new InvalidCredentialsException();

        //check if the student is not allowed to login
        //1. If you a commerce student boot out
        //2. If you are a part 3 not in PRD boot out
        $studentFaculty = $this->facultyID($request->studentID);
        $studentPart = $this->part($request->studentID);
        $programExceptions = SearchConstants::EXCEPTIONS;
        $studentProgramID = $this->programID($request->studentID);

        if ($studentFaculty === FacultyConstants::COMMERCE_FACULTY['faculty_id']) {
            throw new UnauthorizedAccessException();
        } elseif ($studentPart == 3.1 || $studentPart == 3.2) {
            if (!in_array($studentProgramID, $programExceptions))
                throw new UnauthorizedAccessException();
        }

        //get current login timestamp
        $timestamp = $this->authService->getCurrentTimestamp($request->studentID);
        //update the timestamps
        $this->authService->updateTimestamps($timestamp->current_stamp, $request->studentID);

        $user = $this->authService->getUser($request->studentID);
        $userFullName = $this->authService->getStudentFullName($request->studentID);
        $token = $user->createToken('API token of ' . $userFullName)->plainTextToken;

        $dto = new AuthDto($request->studentID, $userFullName, $token);

        return $this->sendData($dto->data());
    }

    public function resetPassword(UserRequest $request)
    {
        $request->validated($request->all());

        $user = $this->authService->updateUserPassword($request->studentID, $request->password);

        if (!$user) throw new UserNotFoundException();;

        $user = $this->authService->getUser($request->studentID);
        $userFullName = $this->authService->getStudentFullName($request->studentID);
        $token = $user->createToken('API token of ' . $userFullName)->plainTextToken;

        $dto = new AuthDto($request->studentID, $userFullName, $token);

        return $this->sendData($dto->data());
    }

    // public function logout($studentID)
    // {
    //     Auth::user()->tokens()->delete();

    //     return $this->sendResponse('Logged out');
    // }

    public function destroy($studentID)
    {
        User::destroy($studentID);
        LoginTimestamps::destroy($studentID);

        return $this->sendResponse('User deleted successfully');
    }
}
