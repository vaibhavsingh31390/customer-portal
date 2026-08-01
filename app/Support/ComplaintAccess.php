<?php

namespace App\Support;

use App\Models\CustomerComplaint;
use Illuminate\Http\Request;

class ComplaintAccess
{
    public static function canView(Request $request, CustomerComplaint $complaint): bool
    {
        $user = $request->session()->get('user');
        $userCode = $user->user_code ?? '';

        if (UserRole::isStaff($userCode)) {
            return true;
        }

        return UserRole::isClient($userCode) && $complaint->client_code === $userCode;
    }

    public static function canClose(Request $request, CustomerComplaint $complaint): bool
    {
        if (in_array($complaint->status, ['CM', 'CL'], true)) {
            return false;
        }

        $user = $request->session()->get('user');
        $userCode = $user->user_code ?? '';

        return UserRole::isAdmin($userCode)
            || (UserRole::isClient($userCode) && $complaint->client_code === $userCode);
    }

    public static function canPostMessage(Request $request, CustomerComplaint $complaint): bool
    {
        if (! self::canView($request, $complaint)) {
            return false;
        }

        return ! in_array($complaint->status, ['CM', 'CL'], true);
    }

    public static function authorRole(string $userCode): string
    {
        if (UserRole::isAdmin($userCode)) {
            return 'admin';
        }

        if (UserRole::isSupport($userCode)) {
            return 'support';
        }

        return 'client';
    }

    public static function isClosed(CustomerComplaint $complaint): bool
    {
        return in_array($complaint->status, ['CM', 'CL'], true);
    }
}
