<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        // Middleware to authenticate API requests, except for 'logout'.
        $this->middleware('auth:api', ['only' => 'logout']);
    }

    /**
     * Authenticate a user.
     *
     * @param  \App\Http\Requests\LoginUserRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     description="Authenticate an existing user",
     *     operationId="login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User login data",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret_password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User authenticated successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="name", type="string", example="Arfaoui Hamza"),
     *                 @OA\Property(property="email", type="string", format="email", example="ogrqsdsdeen@example.net"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-23T05:27:58.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-23T05:27:58.000000Z"),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYjA3MWFhYzJlNmQzYzY3ZjE2OWM4ZTNlMWRkYzZmNjQzMmU1MTU0OTRjMDYwMjIyNjQ3NzE5IiwiaWF0IjoxNjE5NzgxNzEyLjE3MDk4NywibmJmIjoxNjE5NzgxNzEyLjE3MDk4OCwiZXhwIjoxNjIxMzkzNzEyLjE3MDYyNiwic3ViIjoiNiIsInNjb3BlcyBdfQ.v-Z94iF72QcUj3Q-7z7VjI_XZBheWdNzlBabKwtXvXpTjRoM7KGnmT9mcfyH5MNFyErZ8LtWTI2MCGPxfcd9ThFN1c7hhsadgG-2W5Bz0aYwu4ohGSDgM5eEiFZMOUKmscKyspYhSErj6KwSW87VkgsAeqK9jSHy1lsV1ZBhtMnMbC8i3sWsGkw-KY84FnMEFuUduCvEdF_v9R-AqMGi-MrCQJeCZ-F9iHKiqqU0JX2gm5RbH6_KvWgSWryHFoWkAtDhKfSgAZzr6i0Bqk41HT4K7iA2Zu4wWzLjsdDZya3_x0E5wC00QZZUSfHjALGMF55J4nBAbVJiz-zCAdQTdpMXf8nSgXCmYop2bAQCZJv-R9c9lpl8M-iwuk6K1FiMSd1Fy39ogw-MRl6JIFoB08Qr8uv6OqMl1pVU2VWzB-IoUBhhiCNcKQXAWviStVi3T8y6hwdC7eXtQRFx2Lye2GfTcCKp6K_1F-ILaqQLzkD6laCj6ovM4M6LCx-4m7VTYb0YvC_iPeQAK-KDrbNqXjVcgoSg03eGJyFgR_vBzmTZX_UBXUKHN6Ve7ZcRVBzvXt2anG2yaaFd5de2p-5kHvVlYX7MqUwrOanN_yk0xuZbYvcblE04bE3b7rrg2y5mIbP0QOPOYiKU2oEC8wbMG9A0cVym78qZMPMQc9bdY5zHKuJR-4XvFWn3-Zo1flCVRYvZOWM4JAsjntmHlIyLUQbH9P3_yL6hWR3Z-wIzE7ZC71N6W-I9UNeaQKBuK5dGuGddRW_yvY0-2yTrTTTR3Ng0EUas2SajdQz2WhmuwNTJ9H6K4ETOn-MNcHpr6VpD-1hBw5fcio3VZGn_fv71u71qzNdmMzE4tNCI3YVZ2yCuIe_uqqHcKQYvY7aKwF-3uzKhXv_w7MumtW6B6OBmgA3ogNIs2yRzOYzQ8eJwMShiFJ9NuYc"),
     *                 @OA\Property(property="id", type="integer", example=16)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized. Invalid credentials."),
     *         )
     *     )
     * )
     */
    public function login(LoginUserRequest $request)
    {
        $credentials = ['email' => $request->email, 'password' => $request->password];

        // Attempt to log in the user using provided credentials.
        if (auth()->attempt($credentials)) {
            $user = Auth::user();
            $user->token = $user->createToken('Some-Token-Key')->accessToken;

            // Return a JSON response with the authenticated user and access token.
            return response()->json([
                'user' => $user
            ], 200);
        }

        // Return a JSON response for invalid credentials.
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Register a new user.
     *
     * @param  \App\Http\Requests\RegisterUserRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="User Registration",
     *     description="Create a new user",
     *     operationId="register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data",
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret_password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="name", type="string", example="Arfaoui Hamza"),
     *                 @OA\Property(property="email", type="string", format="email", example="ogrqsdsdeen@example.net"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-23T05:27:58.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-23T05:27:58.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=16)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="field_name", type="array",
     *                     @OA\Items(type="string", example="The field_name field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterUserRequest $request)
    {
        // Create a new user in the database with provided registration data.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Return a JSON response to confirm successful user registration.
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Logout the authenticated user and revoke their access tokens.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        // Revoke all access tokens associated with the authenticated user.
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        // Return a JSON response to confirm successful logout.
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
