<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RoommatePreferenceRequest;
use App\Http\Resources\RoommatePreferenceResource;
use App\Http\Services\RoommatePreferenceService;
use App\Traits\HttpResponses;

class RoommatePreferenceController extends Controller
{
    use HttpResponses;

    public function __construct(private RoommatePreferenceService $roommatePreferenceService)
    {
    }

    public function get($studentId)
    {
        $roommatePreferencesExists = $this->roommatePreferenceService->roommatePreferenceExists($studentId);
        if (!$roommatePreferencesExists) return $this->sendData(['data' => ['preference_set' => false]]);

        $roommatePreferences = $this->roommatePreferenceService->getRoommatePreferences($studentId);

        return new RoommatePreferenceResource($roommatePreferences);
    }

    public function create(RoommatePreferenceRequest $request)
    {
        $request->validated($request->all());

        $roommatePreferencesExists = $this->roommatePreferenceService->roommatePreferenceExists($request->student_id);

        if ($roommatePreferencesExists) return $this->sendError('preference settings exist already');

        $this->roommatePreferenceService->createRoommatePreference($request);

        return $this->sendResponse("Roommate preference settings added successfully!");
    }

    public function update(RoommatePreferenceRequest $request)
    {
        $request->validated($request->all());

        $roommatePreferencesExists = $this->roommatePreferenceService->roommatePreferenceExists($request->student_id);

        if (!$roommatePreferencesExists) return $this->sendError('preference settings do not exist!');

        $this->roommatePreferenceService->updateRoommatePreference($request);

        return $this->sendResponse("Roommate preference settings updated successfully!");
    }
}
