<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Callup;
use App\Models\Partners;
use App\Models\Referral;
use App\Models\Prescription;
use App\Models\BookNurseNutritionist;
use App\Models\CaseFile;
use App\Models\NutritionistService;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Authorize the User to receive notifications
Broadcast::channel('User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Check if the logged in Patient is the owner of the case to
//allow for permission to listen to the channel
Broadcast::channel('CaseFile.{id}', function ($user, $id) {
    return (int) $user->id === (int) CaseFile::find($id)->patient->id;
});

Broadcast::channel('ChatSent.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('NutritionChatSent.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


//Check if the Nutritionist Service belongs to the logged in patient
Broadcast::channel('NutritionistService.{nutritionistService}', 
    
function ($user, NutritionistService $nutritionistService)
     {
      return (int) $user->id === (int) $nutritionistService->patient_id;
     }
);

Broadcast::channel('CallMade.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('CallEnded.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('CallDeclined.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ListPharmacies.{id}', function ($user, $id) {
    return (int) $user->id === (int) CaseFile::find($id)->patient_id;
});

Broadcast::channel('ListDiagnostics.{id}', function ($user, $id) {
    return (int) $user->id === (int) CaseFile::find($id)->patient_id;
});

//Check if the created case is for the doctor's Specialty 
Broadcast::channel('CaseCreated.{specialtyId}', function ($user, $specialtyId) {
    return (int) $user->doctorProfile->specialty_id === (int) $specialtyId;
});

Broadcast::channel('ambulanceCallup', function ($partners, $callup) {
    return Auth::check();
});

Broadcast::channel('chat', function ($user) {
    return Auth::check();
});

Broadcast::channel('referral', function ($referral, $partners) {
    return Auth::check();
});

// Broadcast::channel('Prescription.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
// Broadcast::channel('Prescription.{id}', function ($user, $id) {
//     //return (int) $user->id === (int) Prescription::find($id)->partners_id;
//     return true;
// });
Broadcast::channel('diagnoticsAppointment', function ($appointment) {
    return Auth::check();
});

Broadcast::channel('diagnoticsReport', function ($report, $appointment) {
    return Auth::check();
});

Broadcast::channel('requestNurseOrNutritionist', function ($booking, $partners) {
    return Auth::check();
});

Broadcast::channel('caseFile', function ($caseFile) {
    return Auth::check();
});
