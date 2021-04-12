<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetEvent;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Resources\UserDetailsResource;
use App\Models\User;
use App\Models\File;
use App\RepositoryContracts\UserRepository;
use App\ServiceContracts\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// use App\User;

class UserManagementController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserManagementService
     */
    private $userManagementService;

    public function __construct(UserRepository $userRepository,
                                UserManagementService $userManagementService)
    {
        $this->userRepository = $userRepository;
        $this->userManagementService = $userManagementService;
    }

    public function validateEmail()
    {
        if(!$this->userRepository->verifyUser(request()->token)){

            return $this->failedResponse('Invalid token.', 422);

        }

        User::where('email_token',request()->token)->first()->update(['verified'=>true,'email_token'=>null]);

        return $this->successfulResponse(200, null, 'Email verified succesfully.');
    }

    public function resendMail()
    {
        request()->validate([
            "email"=>"required|exist_column:users,email"
        ]);

        try{
            $token = Str::random(80);

            User::whereEmail(request()->email)->first()->update(['email_token'=>$token]);

            $this->userRepository->sendVerificationEmail(request()->email, $token);

            return $this->successfulResponse(200, null, 'Verification link sent succesfully.');

        }catch(\Exception $e){

            return $this->failedResponse('Something went wrong.', 500);
        }
    }

    /**
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function createUser(CreateUserRequest $request)
    {
        $attributes = $request->all();
        $result = $this
            ->userManagementService
            ->createUser($attributes);

        //send mail
        return $this->successfulResponse(201, $result);
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $user = $this->userManagementService->updateUser($user, $request->all());

        return $this->successfulResponse(200, $user);
    }

    public function showUserProfile()
    {
        $user = auth()->user();

        return response(["data" => $user], 200);
    }

    public function showUserProfileImage($user_id)
    {
        $user = User::find($user_id);
        $profile_image = $user->files()->first();
        if ($profile_image) {

            if(env('APP_ENV') == 'production'){
                return Storage::disk('profile_pictures')->response($profile_image->name);
         
             }
            // $path = storage_path() . '/app//' . $profile_image->path;
            // return response()->file($path);
            return response()->file(storage_path("app/public/profile_picture/{$profile_image->name}"));
        }
        return response("no_image", 400) ;
    }

    public function me(){
        $user = request()->user();
        $user['roles'] = $user->roles;
        return response(["data" => $user], 200);
    }

    public function onForgotPassword(PasswordResetRequest $request)
    {
        $response = $this->userRepository->generateUserRefreshToken($request->email);
        PasswordResetEvent::dispatch($response->user, $response->passwordReset->token);
        return $this->successfulResponse(202, null, 'We have e-mailed your password reset link!');
    }

    public function doPasswordReset($token, ChangePasswordRequest $request)
    {
        $isPasswordReset = $this
            ->userManagementService
            ->doPasswordReset($token, $request->get('password'));

        if ($isPasswordReset) {
            return $this->successfulResponse();
        }

        return $this->failedResponse('Failed updating user password.', 500);
    }

    public function authenticatedUser()
    {
        $user = User::with('roles', 'partners')->where('id', '=', auth()->id())->first();
        return $this->successfulResponse(200, new UserDetailsResource($user));
    }

   /* public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'image' => 'required|image']);
        $image = $request->file('image');
        $fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . strtotime(now()) . "." . $image->clientExtension();
        $fileContents = file_get_contents($image->getRealPath());
        Storage::disk('profile_pictures')->put($fileName, $fileContents);
        auth()->user()->update([ 'profile_picture' => $fileName ]);
        return $this->successfulResponse(202);
    }*/

     public function profilePicsUpload(Request $request, $userId)
    {
        $rules = array('profile_image' => 'required|max:100|image');

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            // if ($request->hasfile('profile_image')) {

            //     $newlyUploadedFile = $request->file('profile_image');
            //     $originalName = $newlyUploadedFile->getClientOriginalName();
                
            //     $user = User::find($userId);

            //      if ($oldFile = $user->files ) {
                    
            //         if (\Storage::exists($oldFile->path)) {
            //             \Storage::delete($oldFile->path);
            //             $path = \Storage::putFile('profile', $newlyUploadedFile);
            //             $oldFile->name = $originalName;
            //             $oldFile->path = $path;
            //             $oldFile->save();
            //         }

            //         else {
            //             $path = \Storage::putFile('profile', $newlyUploadedFile);
            //             $oldFile->name = $originalName;
            //             $oldFile->path = $path;
            //             $oldFile->save();
            //         }
            //     } else {
            //         $path = \Storage::putFile('profile', $newlyUploadedFile);

            //         $file = new File;
            //         $file->name = $originalName;
            //         $file->path = $path;

            //         $user->files()->save($file);
            //     }
            //     return response()->json(['message'=>'Profile Picture Succcessfully uploaded'], 200);
            // } else {
            //     return response()->json(['message'=>'Not a File'], 401);
            // }

            if ($request->hasfile('profile_image')) {

                $file = $request->file('profile_image');
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . strtotime(now()) . "." . $file->clientExtension();
                $fileContents = file_get_contents($file->getRealPath());
                
                $user = User::find($userId);

                 if ($oldFile = $user->files ) {
                    
                    if (\Storage::disk('profile_pictures')->exists($oldFile->path)) {
                        \Storage::disk('profile_pictures')->delete($oldFile->path);
                        $path= \Storage::disk('profile_pictures')->put($fileName, $fileContents);
                       
                        $oldFile->name = $fileName;
                        $oldFile->path = $path;
                        $oldFile->save();
                    }

                    else {
                        $path= \Storage::disk('profile_pictures')->put($fileName, $fileContents);
                        $oldFile->name = $fileName;
                        $oldFile->path = $path;
                        $oldFile->save();
                    }
                } else {

                    $path= \Storage::disk('profile_pictures')->put($fileName, $fileContents);
                    $file = new File;
                    $file->name = $fileName;
                    $file->path = $path;

                    $user->files()->save($file);
                    
                }
                return response()->json(['message'=>'Profile Picture Succcessfully uploaded'], 200);
            } else {
                return response()->json(['message'=>'Not a File'], 401);
            } 
        } else {
            return back()->withErrors($validator)->withInput();
        }
    }
}
