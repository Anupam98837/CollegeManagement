<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function adminLogin(Request $request)
    {
        Log::info('Admin login request received', ['request' => $request->all()]);

        // Validate input
        $validator = Validator::make($request->all(), [
            'identifier' => 'required', // Email or phone
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()->first()]);
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        // Check if admin exists using either email or phone
        $admin = DB::table('admins')
            ->where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();

        if (!$admin) {
            Log::error('Admin not found', ['identifier' => $request->identifier]);
            return response()->json(['status' => 'error', 'message' => 'Admin not found.'], 404);
        }

        Log::info('Admin found', ['req pass' => $request->password]);
        Log::info('Admin found', ['admin pass' => $admin->password]);
        // Verify password
        // if (!Hash::check($request->password, $admin->password)) {
        //     Log::error('Invalid credentials', ['admin_id' => $admin->id]);
        //     return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        // }
        if ($request->password != $admin->password) {
            Log::error('Invalid credentials', ['admin_id' => $admin->id]);
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        Log::info('Password verification successful', ['admin_id' => $admin->id]);

        // Generate API Token
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        // Delete previous tokens for security
        DB::table('personal_access_tokens')->where('tokenable_id', $admin->id)->delete();

        // Store the new token
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'admins',
            'tokenable_id' => $admin->id,
            'name' => 'admin_auth',
            'token' => $hashedToken,
            'abilities' => json_encode(['*']),
            'designation' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Token generated and stored', ['admin_id' => $admin->id]);

        return response()->json([
            'status' => 'success',
            'admin_token' => $token,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'designation' => 'Admin',
            ]
        ], 200);
    }

    public function adminLogout(Request $request)
    {
        Log::info('Admin logout request received', ['token' => $request->token]);

        // Validate token presence
        if (!$request->token) {
            Log::warning('Logout failed: No token provided');
            return response()->json(['status' => 'error', 'message' => 'No token provided'], 400);
        }

        // Hash the token to match stored format
        $hashedToken = hash('sha256', $request->token);

        // Check if token exists
        $tokenExists = DB::table('personal_access_tokens')->where('token', $hashedToken)->first();

        if (!$tokenExists) {
            Log::error('Invalid token during logout', ['token' => $request->token]);
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
        }

        // Delete the token from the database
        DB::table('personal_access_tokens')->where('token', $hashedToken)->delete();

        Log::info('Admin logged out successfully', ['admin_id' => $tokenExists->tokenable_id]);

        return response()->json(['status' => 'success', 'message' => 'Logged out successfully'], 200);
    }

    // New Function: Admin Upload Image
    public function adminUploadImage(Request $request)
    {
        Log::info('Admin image upload request received', ['request' => $request->all()]);

        if (!$request->hasFile('attachment')) {
            Log::warning('No attachment provided for image upload');
            return response()->json(['status' => 'error', 'message' => 'No attachment provided'], 400);
        }

        $attachmentPath = null;
        $file = $request->file('attachment');
        // Generate a unique file name.
        $uniqueFileName = uniqid('admin_') . '.' . $file->getClientOriginalExtension();
        // Save file to public/assets/admin_documents directory.
        $destinationPath = public_path('assets/admin_documents');
        $file->move($destinationPath, $uniqueFileName);
        $attachmentPath = 'assets/admin_documents/' . $uniqueFileName;

        Log::info('Image uploaded successfully', ['attachmentPath' => $attachmentPath]);

        return response()->json(['status' => 'success', 'attachmentPath' => $attachmentPath], 200);
    }

    // New Function: Admin Change Password
    public function adminChangePassword(Request $request)
    {
        Log::info('Admin change password request received', ['request' => $request->all()]);

        // Validate input
        $validator = Validator::make($request->all(), [
            'admin_id'    => 'required',
            'old_password'=> 'required',
            'new_password'=> 'required|min:6'
        ]);

        if ($validator->fails()) {
            Log::warning('Change password validation failed', ['errors' => $validator->errors()->first()]);
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        // Retrieve admin record by admin_id
        $admin = DB::table('admins')->where('id', $request->admin_id)->first();
        if (!$admin) {
            Log::error('Admin not found for password change', ['admin_id' => $request->admin_id]);
            return response()->json(['status' => 'error', 'message' => 'Admin not found'], 404);
        }

        // Verify old password: first check using hash then plain-text fallback.
        if (Hash::check($request->old_password, $admin->password)) {
            Log::info('Old password verified using hash check', ['admin_id' => $admin->id]);
        } else if ($request->old_password == $admin->password) {
            Log::info('Old password matched plain text, will update to hashed', ['admin_id' => $admin->id]);
        } else {
            Log::error('Old password does not match', ['admin_id' => $admin->id]);
            return response()->json(['status' => 'error', 'message' => 'Old password does not match'], 401);
        }

        // Hash the new password and update the admin record.
        $newHashedPassword = Hash::make($request->new_password);
        DB::table('admins')->where('id', $admin->id)->update(['password' => $newHashedPassword]);

        Log::info('Password changed successfully', ['admin_id' => $admin->id]);

        return response()->json(['status' => 'success', 'message' => 'Password changed successfully'], 200);
    }
}
