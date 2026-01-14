<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = \App\Models\Admin::where('email', $request->email)->first();
        if (!$user) {
            $user = \App\Models\Manager::where('email', $request->email)->first();
        }

        if (!$user) {
            return response()->json([
                'message' => 'البريد الإلكتروني غير موجود'
            ], 404);
        }

    
        $token = Str::random(6); 

        
        PasswordResetToken::where('email', $request->email)->delete();

        
        PasswordResetToken::create([
            'email' => $request->email,
            'token' => Hash::make($token),
            'expires_at' => Carbon::now()->addMinutes(30)
        ]);

        
        Mail::raw("كود التحقق الخاص بك هو: $token\n\nاستخدم هذا الكود لإعادة تعيين كلمة المرور. ينتهي الكود خلال 30 دقيقة.", function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('استعادة كلمة المرور');
        });

        return response()->json([
            'message' => 'تم إرسال رمز الاستعادة إلى بريدك الإلكتروني'
        ], 200);
    }

    
    public function verifyToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string'
        ]);

        
        $resetToken = PasswordResetToken::where('email', $request->email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetToken || !Hash::check($request->token, $resetToken->token)) {
            return response()->json([
                'message' => 'الرمز غير صالح أو منتهي الصلاحية'
            ], 400);
        }

        return response()->json([
            'message' => 'الرمز صالح',
            'token' => $resetToken->token
        ], 200);
    }


    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        
        $resetToken = PasswordResetToken::where('email', $request->email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$resetToken || !Hash::check($request->token, $resetToken->token)) {
            return response()->json([
                'message' => 'الرمز غير صالح أو منتهي الصلاحية'
            ], 400);
        }


        $user = \App\Models\Admin::where('email', $request->email)->first();
        if (!$user) {
            $user = \App\Models\Manager::where('email', $request->email)->first();
        }
        $user->password = Hash::make($request->password);
        $user->save();

        
        PasswordResetToken::where('email', $request->email)->delete();

        return response()->json([
            'message' => 'تم إعادة تعيين كلمة المرور بنجاح'
        ], 200);
    }
}
