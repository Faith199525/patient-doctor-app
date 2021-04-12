<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BankListController;
use App\Http\Controllers\CaseManagementController;
use App\Http\Controllers\DoctorManagementController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DiagnosticReportController;
use App\Http\Controllers\AmbulanceCallUpController;
use App\Http\Controllers\BookNurseController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\RequestedPartnersController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VideoChatController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\MedicalScreeningController;
use App\Http\Controllers\OpticalServiceController; 
use App\Http\Controllers\GeneralPractitionerServiceController;
use App\Http\Controllers\DentalServiceController;
use App\Http\Controllers\NutritionistServiceController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NutritionChatController;
use App\Http\Controllers\CallController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Broadcast::routes(['middleware' => ['auth:api']]);

Route::post('/login', [AuthenticationController::class, 'login']);
Route::middleware('auth:api')->get('/user', 'UserManagementController@me');

Route::post('/signup', [UserManagementController::class, 'createUser']);

Route::post('/doctors/signup', [DoctorManagementController::class, 'createDoctor']);
Route::post('/partners/signup', [PartnersController::class, 'createPartner']);
Route::post('/request/partners/signup', [RequestedPartnersController::class, 'createPartner']);
Route::post('/logout', [LogoutController::class, 'logout']);

Route::get('/email-verification/{token}', [UserManagementController::class, 'validateEmail']);
Route::post('/email-verification/resend/', [UserManagementController::class, 'resendMail']);
Route::post('/password-reset/{token}', [UserManagementController::class, 'doPasswordReset']);
Route::post('/forgot-password', [UserManagementController::class, 'onForgotPassword']);


Route::get('/specialties', [SpecialtyController::class, 'index']);
Route::get('/banks', [BankListController::class, 'index']);
Route::get('/user/profile_image/{user_id}', [UserManagementController::class, 'showUserProfileImage']);

Route::middleware(['auth:api'])->group(function () {

    Route::post('/cases', [CaseManagementController::class, 'createCase'])->middleware('role:patient');
   // Route::get('/cases', [CaseManagementController::class, 'getMyActiveCase'])->middleware('role:patient');
    Route::patch('/cases/{caseFile}', [CaseManagementController::class, 'acceptCase'])->middleware('role:doctor');
    Route::patch('/cases/{caseFile}/close', [CaseManagementController::class, 'closeCase'])->middleware('role:doctor');
    Route::post('cases/{caseFile}/messages', [MessageController::class, 'send']);
    Route::get('cases/{caseFile}/messages', [MessageController::class, 'receive']);

    Route::post('/cases/{caseFile}', [CaseManagementController::class, 'updateCase']);
    Route::get('/cases/{user}/patient', [CaseManagementController::class, 'getPatientRecentCases']);
    Route::get('/cases/active', [CaseManagementController::class, 'getActiveCases'])->middleware('role:patient');
    Route::get('/cases/completed', [CaseManagementController::class, 'getCompletedCases'])->middleware('role:patient');
    Route::get('/cases/doctor-active', [CaseManagementController::class, 'getActiveCasesForDoctor'])->middleware('role:doctor');
    Route::get('/cases/doctor-completed', [CaseManagementController::class, 'getCompletedCasesForDoctor'])->middleware('role:doctor');
    Route::get('/cases/doctor', [CaseManagementController::class, 'getAllCasesForDoctor'])->middleware('role:doctor');

    Route::get('cases/general-room', [CaseManagementController::class, 'showUnattendedGeneralCases'])->middleware('role:doctor');
    Route::get('cases/specialist-room', [CaseManagementController::class, 'showUnattendedSpecialistCases'])->middleware('role:doctor');
    Route::get('cases/{caseFile}', [CaseManagementController::class, 'showSingleCase']);


    Route::post('prescriptions', [PrescriptionController::class, 'createPrescription'])->middleware('role:doctor');
    Route::get('/newPrescriptions', [PrescriptionController::class, 'getNewPrescription']);
    Route::get('/pending', [PrescriptionController::class, 'getPending']);
    Route::get('/completed', [PrescriptionController::class, 'getCompleted']);
    Route::get('/patient/newPrescriptions', [PrescriptionController::class, 'getPatientNewPrescription']);
    Route::get('/patient/pending', [PrescriptionController::class, 'getPatientPending']);
    Route::get('/patient/completed', [PrescriptionController::class, 'getPatientCompleted']);
    Route::get('/patient/declined', [PrescriptionController::class, 'getDeclined']);
    // Route::get('prescriptions', [PrescriptionController::class, 'getAllPrescription']);
    Route::get('/pharmacies', [PrescriptionController::class, 'getPharmacies']);
    Route::patch('prescriptions/{prescription}', [PrescriptionController::class, 'updatePrescription']);
    Route::patch('prescriptions/{prescription}/accept', [PrescriptionController::class, 'patientAcceptPrescriptionCost']);
    Route::get('prescriptions/{prescription}', [PrescriptionController::class, 'getPrescription']);
    Route::patch('prescriptions/{prescription}/pharmacies', [PrescriptionController::class, 'attachPharmacyToPrescription']);
    Route::patch('/prescriptions/{prescription}/decline', [PrescriptionController::class, 'pharmacyDeclinePrescription']);
    Route::patch('/prescriptions/{prescription}/patient/decline', [PrescriptionController::class, 'patientDeclinePrescriptionCost']);
    Route::patch('prescriptions/{prescription}/drugs', [PrescriptionController::class, 'updateDrugs']);
    Route::get('prescriptions/unassigned/{casefile}', [PrescriptionController::class, 'getUnassignedPrescriptions']);
    Route::delete('prescriptions/{prescription}/drugs/{drug}', [PrescriptionController::class, 'deleteDrug']);
    Route::post('/payment/response', 'PaymentController@payForDrugsResponse');

    Route::post('referrals', [ReferralController::class, 'createReferral']);
    Route::get('referrals/{referral}', [ReferralController::class, 'getSingleReferral']);
    Route::patch('referrals/{referral}/accept', [ReferralController::class, 'acceptReferral']);
    Route::get('/accepted/referrals', [ReferralController::class, 'getALlAcceptedReferral']);
    Route::get('/active/referrals', [ReferralController::class, 'getALlActiveReferral']);

    Route::get('/me', [UserManagementController::class, 'authenticatedUser']);
    //Route::patch('/profile-picture', [UserManagementController::class, 'updateProfilePicture']);

    /**
     * New Endpoints by Tosin
     */
    Route::patch('/cases/{caseFile}/observations', [CaseManagementController::class, 'updateCaseFile']);
    Route::get('/case/{id}/patient', [CaseManagementController::class, 'getPatient']);
    Route::get('/case/{id}/doctor', [CaseManagementController::class, 'getDoctor']);

    Route::get('/cases/{id}/prescriptions', [CaseManagementController::class, 'getPrescription']);

    // Route::post('/subscriptions/get-paystack-url', [SubscriptionController::class, 'getCheckoutUrl']);
    // Route::get('/payment/verification/{trxref}', [SubscriptionController::class, 'processPayment']);
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'getSubscriptionPlans']);
    Route::get('/subscriptions', [SubscriptionController::class, 'getActiveSubscription']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('subscription/payment', 'PaymentController@subscriptionPayment');


    Route::post('/update/patient/{user}', [UserManagementController::class, 'updateUser']);
    Route::get('/view/profile', [UserManagementController::class, 'showUserProfile']);
    Route::post('/upload/patient/{userId}/profilePic', [UserManagementController::class, 'profilePicsUpload']);


    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::get('appointments', [AppointmentController::class, 'getAppointments']);
    
    // Route::post('/update/appointments/{appointment}', [AppointmentController::class, 'updateAppointment']);
    // Route::post('/acceptORdecline/appointments/{appointment}', [AppointmentController::class, 'acceptORdecline']);
    // Route::get('/view/appointment/{appointment}', [AppointmentController::class, 'show']);

    Route::post('/callUp', [AmbulanceCallUpController::class, 'Callup'])->middleware('role:patient');
    Route::patch('/accept/callup/{callup}', [AmbulanceCallUpController::class, 'accept'])->middleware('role:ambulance');
    Route::get('/view/callup/{callup}', [AmbulanceCallUpController::class, 'show']);
    Route::patch('/completeCallup/{callup}', [AmbulanceCallUpController::class, 'completeACallupRequest']);
    Route::get('/view/pendingCallups', [AmbulanceCallUpController::class, 'showAllPendingCallups']);
    Route::get('/view/acceptedCallups', [AmbulanceCallUpController::class, 'showAllAcceptedCallups']);
    Route::get('/view/completedCallups', [AmbulanceCallUpController::class, 'showAllCompletedCallups']);
    Route::get('/view/patientCallups', [AmbulanceCallUpController::class, 'getPatientCallups']);

    Route::post('/book/nurse', [BookNurseController::class, 'book'])->middleware('role:patient');
    Route::patch('/confirm/booking/{booking}', [BookNurseController::class, 'confirm']);
    //Route::post('/update/booking/{booking}', [BookNurseController::class, 'updatebooking']);
    Route::get('/booking/{booking}', [BookNurseController::class, 'show']);
    Route::patch('/complete/booking/{booking}', [BookNurseController::class, 'completeABooking']);
    Route::get('/confirmed/bookings', [BookNurseController::class, 'showAllConfirmed']);
    Route::get('/completed/bookings', [BookNurseController::class, 'showAllCompleted']);
    Route::get('/new/bookings', [BookNurseController::class, 'showAllNew']);
    Route::get('/list/nurses', [BookNurseController::class, 'listNurses']);
    //Route::patch('/refer/nurse/{booking}', [BookNurseController::class, 'referNurse']);
    Route::post('/book/nurse/payment', 'PaymentController@bookNurse');


    Route::post('/partners/update', [PartnersController::class, 'updatePartner']);
    Route::get('/partners', [PartnersController::class, 'index']);
    Route::get('/partners/{partner}', [PartnersController::class, 'show']);
    Route::get('/partner/profile', [PartnersController::class, 'showProfile']);
    Route::get('/partners-type', [PartnersController::class, 'getPartnersByType']);
    Route::post('/request/partners/update', [RequestedPartnersController::class, 'updatePartner']);
    Route::get('/request/partner/profile', [RequestedPartnersController::class, 'showProfile']);

    Route::post('messages/{user}', [MessageController::class, 'sendMessageToUser']);
    Route::get('/messages/{user}', [MessageController::class, 'receiveMessageFromUser']);

    Route::get('/medical-history', [MedicalHistoryController::class, 'getMyMedicalHistory'])->middleware('role:patient');
    Route::post('/medical-histories', [MedicalHistoryController::class, 'storeOrUpdate'])->middleware('role:patient');
    Route::get('/medical-histories/{medicalHistory}', [MedicalHistoryController::class, 'show']);

    Route::post('/doctor-profile', [DoctorManagementController::class, 'updateDoctorProfile'])->middleware('role:doctor');
    Route::get('/doctor-profile', [DoctorManagementController::class, 'showDoctorProfile'])->middleware('role:doctor');

    // Route::get('/video_chat', [App\Http\Controllers\VideoChatController::class, 'index']);
    Route::post('/auth/video_chat', [VideoChatController::class, 'auth']);

    Route::get('verify-payment', 'PaymentController@verifyPayment');
    Route::post('payments', 'PaymentController@store');
    
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::get('/markAsRead', [NotificationController::class, 'markAsRead']);
    Route::get('/recent-notifications', [NotificationController::class, 'recent']);

    Route::post('/diagnosis', [DiagnosisController::class, 'sendTest']);
    Route::get('/diagnosistests', [DiagnosisController::class, 'getDiagnosticTests']);
    Route::get('/list/diagnostic', [DiagnosisController::class, 'listDiagnostics']);
    Route::patch('/refer/diagnostic/{diagnosis}', [DiagnosisController::class, 'referDiagnostic']);
    Route::get('/unassigned/diagnosis/{case_file_id}', [DiagnosisController::class, 'getUnassignedDiagosis']);
    Route::get('/tests/assigned', [DiagnosisController::class, 'getTestsAssignedToMe']);
    Route::get('/diagnosis/{diagnosis}', [DiagnosisController::class, 'getDiagnosis']);
    Route::post('/upload/result/{test}', [DiagnosisController::class, 'uploadTestResult']);
    Route::get('/complete/result/{diagnosis}', [DiagnosisController::class, 'complete']);
    Route::get('/patient/new/test', [DiagnosisController::class, 'getPatientNewTest']);
    Route::get('/patient/complete/test', [DiagnosisController::class, 'getPatientCompletedTest']);
    Route::get('/diagnostic/test/completed', [DiagnosisController::class, 'getCompletedTestsByDiagnostic']);
    Route::get('/download/test/result/{test}', [DiagnosisController::class, 'getDownload']);
    // Route::get('/view/test/result/{test}', [DiagnosisController::class, 'viewResult']);
    Route::post('payment/tests', 'PaymentController@payForTestsResponse');

    Route::get('screening-centers', [MedicalScreeningController::class, 'screeningCenters']);
    Route::post('tests', [MedicalScreeningController::class, 'store']);

    Route::get('optical-centers', [OpticalServiceController::class, 'opticalCenters']);
    Route::post('optical-services', [OpticalServiceController::class, 'store']);

    Route::get('general-practitioners', [GeneralPractitionerServiceController::class, 'index']);
    Route::post('general-practitioners', [GeneralPractitionerServiceController::class, 'store']);

    Route::get('dental-centers', [DentalServiceController::class, 'index']);
    Route::post('dental-services', [DentalServiceController::class, 'store']);


    Route::get('nutritionists', [NutritionistServiceController::class, 'index']);
    Route::get('nutritionist-services', [NutritionistServiceController::class, 'nutritionistServices']);
    Route::get('nutritionist-services/{nutritionistService}', [NutritionistServiceController::class, 'show']);
    Route::post('nutritionist-services', [NutritionistServiceController::class, 'store']);
    Route::post('nutritionist-services/{nutritionistService}', [NutritionistServiceController::class, 'accept']);

    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::post('optical-appointments', [AppointmentController::class, 'storeOptical']);
    Route::post('dental-appointments', [AppointmentController::class, 'storeDental']);
    Route::post('gp-appointments', [AppointmentController::class, 'storeGp']);
    Route::post('nurse-appointments', [AppointmentController::class, 'storeNurse']);
    Route::get('appointments', [AppointmentController::class, 'getPatientAppointments'])->middleware('role:patient');
    Route::get('center-appointments', [AppointmentController::class, 'getCenterAppointments']);
    Route::post('appointments/{appointment}/accept', [AppointmentController::class, 'accept']);
    Route::post('appointments/{appointment}/decline', [AppointmentController::class, 'decline']);
    Route::post('appointments/{appointment}/book', [AppointmentController::class, 'book']);
    Route::get('appointments/{appointment}', [AppointmentController::class, 'show']);

    Route::post('chats/{receiverId}', [ChatController::class, 'store']);
    Route::get('chats', [ChatController::class, 'index']);

    Route::post('nutrition-chats/{receiverId}', [NutritionChatController::class, 'store']);
    Route::get('nutrition-chats', [NutritionChatController::class, 'index']);

    Route::get('countries', [CountryController::class, 'getCountries']);
    Route::get('staties', [CountryController::class, 'getStates']);

    Route::get('call/{id}', [CallController::class, 'call']);
    Route::get('end-call/{id}', [CallController::class, 'endCall']);
    Route::get('decline-call/{id}', [CallController::class, 'declineCall']);
});

Route::get('/view/test/result/{test}', [DiagnosisController::class, 'viewResult']);
Route::get('/terms', function () {
    return response()->file(public_path("termsCondition/t-c.pdf"));
});