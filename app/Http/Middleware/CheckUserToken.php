<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\SqlHelper;
use App\Support\UserRole;
use Closure;

class CheckUserToken
{
    public function handle($request, Closure $next)
    {
        if (! $request->session()->has('user_token')) {
            return redirect()->route('login.form')->withErrors([
                'message' => 'You are not logged in.',
            ]);
        }

        $token = $request->session()->get('user_token');
        $userId = decrypt($token);
        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login.form')->withErrors([
                'message' => 'Invalid user token.',
            ]);
        }

        if (UserRole::isStaff($user->user_code)) {
            $nameRow = SqlHelper::selectOne(
                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS).' WHERE working_status = ? AND engineer_code = ? ORDER BY 1',
                ['WK', $user->user_code]
            );
            $name = $nameRow->name ?? null;
        } else {
            $nameRow = SqlHelper::selectOne(
                'SELECT name FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' AND client_code = ? ORDER BY 1",
                [$user->user_code]
            );
            $name = $nameRow->name ?? null;
        }

        $request->session()->put('user', $user);
        $request->session()->put('name', $name);

        return $next($request);
    }
}
