<?php

namespace App\Http\Controllers\User;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\AuthUserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Throwable;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/auth/login",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="remember", type="boolean", example="true"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *     ),
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     ),
     * ),
     * )
     *
     * Login a user with email
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            return response()->json([
                'message' => Lang::get('auth.loggedIn')
            ]);
        }
        throw ValidationException::withMessages([
            'email' => [Lang::get('auth.failed')],
        ]);
    }

    /**
     * @OA\Post(
     * path="/auth/token",
     * description="Generate Token to use API docs",
     * operationId="authToken",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success")
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later.")
     *     )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later.")
     *     )
     * ),
     * )
     *
     * Generate token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function token(Request $request): mixed
    {
        $user = User::query()->where('email', $request->get('email'))->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ["We can't find a user with that email address."],
            ]);
        }
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email address is not verified.',
            ], 403);
        }
        if (!Hash::check($request->get('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }
        event(new Login('web', $user, true));

        return $user->createToken('web')->plainTextToken;
    }

    /**
     * @OA\Get(
     * path="/auth/me",
     * description="Get loggedIn user",
     * operationId="authMe",
     * tags={"Auth"},
     * security={ {"sanctum": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Authentication response",
     *    @OA\JsonContent(
     *       @OA\Property(property="user", type="object", ref="#/components/schemas/AuthResponse")
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later.")
     *     )
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *     )
     * ),
     * )
     *
     * Get auth user response.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'user' => AuthUserResource::make(Auth::user())
        ]);
    }

    /**
     * @OA\Post(
     * path="/auth/logout",
     * description="Logout and desctory session",
     * operationId="authLogout",
     * tags={"Auth"},
     * security={ {"sanctum": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Successfully Logged Out",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Successfully Logged Out!"),
     *     ),
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *     ),
     * ),
     * )
     *
     * Logout user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return response()->json([
            'message' => Lang::get('auth.loggedOut')
        ]);
    }

    /**
     * @OA\Post(
     * path="/auth/logout-by-token",
     * description="Logout and delete token",
     * operationId="authLogoutByToken",
     * tags={"Auth"},
     * security={ {"sanctum": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Successfully Logged Out",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Successfully Logged Out!"),
     *     ),
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *     ),
     * ),
     * )
     *
     * Logout user by token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutByToken(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        event(new Logout('web', $request->user()));

        return response()->json([
            'message' => Lang::get('auth.loggedOut')
        ]);
    }

    /**
     * @OA\Post(
     * path="/auth/forgot-password",
     * description="Forgot password request",
     * operationId="authForgotPassword",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Please enter email address",
     *    @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *     ),
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Validation Error Response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     ),
     * ),
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
            return $status === Password::RESET_LINK_SENT
                ? response()->json([
                    'message' => __($status)
                ])
                : response()->json([
                    'message' => __($status)
                ], 400);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('auth.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     * path="/auth/reset-password",
     * description="Reset password request",
     * operationId="authResetPassword",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Please enter valid information",
     *    @OA\JsonContent(
     *       required={"token", "email", "password", "password_confimation"},
     *       @OA\Property(property="token", type="string", format="string", example="1231231232321kjkljkljklk"),
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *       @OA\Property(property="password", type="string", format="string", example="PassWord12345"),
     *       @OA\Property(property="password_confirmation", type="string", format="string", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *     ),
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );
            return $status === Password::PASSWORD_RESET
                ? response()->json([
                    'message' => __($status)
                ])
                : response()->json([
                    'message' => __($status)
                ], 400);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('auth.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     * path="/auth/email/verify/{id}/{hash}",
     * description="Verify email address",
     * operationId="authVerifyEmail",
     * tags={"Auth"},
     * @OA\Parameter(
     *    description="ID of User",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\Parameter(
     *    description="Hash value",
     *    in="path",
     *    name="hash",
     *    required=true,
     *    example="12345asdfg",
     *    @OA\Schema(
     *       type="string",
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *     ),
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Not Found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not Found."),
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        try {
            $user_id = $request->route('id');
            $user = User::query()->where('id', '=', $user_id)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found!'
                ], 404);
            }
            if (!hash_equals((string)$request->route('hash'),
                sha1($user->getEmailForVerification()))) {
                return response()->json([
                    'message' => 'User not found!'
                ], 404);
            }

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();

                event(new Verified($user));
            }

            return response()->json([
                'message' => 'Email verified successfully'
            ]);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('auth.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     * path="/auth/email/resend/{email}",
     * description="Resend verification email",
     * operationId="authResendVerifyEmail",
     * tags={"Auth"},
     * @OA\Parameter(
     *    description="Email address",
     *    in="path",
     *    name="email",
     *    required=true,
     *    example="user@example.com",
     *    @OA\Schema(
     *       type="string",
     *       format="email"
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *     ),
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Not Found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not Found."),
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * )
     *
     * @param $email
     * @return JsonResponse
     */
    public function resend($email): JsonResponse
    {
        try {
            // find user
            $user = User::query()->where('email', '=', $email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found!'
                ], 404);
            }
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Verification link sent!'
            ]);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('auth.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

}
