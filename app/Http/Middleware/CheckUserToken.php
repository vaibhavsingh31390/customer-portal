<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\DB;

class CheckUserToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('user_token')) {
            return redirect()->route('login.form')->withErrors([
                'message' => 'You are not logged in.',
            ]);
        }
        $token = $request->session()->get('user_token');
        $userId = decrypt($token);
        $user = User::find($userId);

        if ($user->eng_cd && preg_match('/^[S]/', $user->eng_cd)) {
            $name =  DB::select("SELECT ENG_NAME FROM ENG_MASTER WHERE WORKING_ST='WK' AND DEPARTMENT='SWE' AND ENG_CD = '$user->eng_cd' ORDER BY 1");
            $name =  @$name[0]->eng_name;
        } else {
            $name =  DB::select("SELECT CLIENT_NAME FROM CELINT_MASTER WHERE ERP_VERT='TERMS' AND CLIENT_CD = '$user->eng_cd' ORDER BY 1");
            $name =  @$name[0]->client_name;
        }

        if (!$user) {
            return redirect()->route('login.form')->withErrors([
                'message' => 'Invalid user token.',
            ]);
        }
        if (!$request->session()->has('user')) {
            $request->session()->put('user', $user);
        }

        if (!$request->session()->has('name')) {
            $request->session()->put('name', $name);
        }

        // Proceed to the next request
        return $next($request);
    }
}
