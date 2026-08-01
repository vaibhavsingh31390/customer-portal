<?php

namespace App\Http\Admin;

use App\Support\SqlHelper;
use App\Support\UserRole;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(Request $request)
    {
        $clients = SqlHelper::select(
            'SELECT client_code, name, email FROM '.SqlHelper::table(SqlHelper::TABLE_CLIENTS)." WHERE erp_vertical = 'TERMS' ORDER BY name"
        );
        $users = DB::table('portal_users')->orderBy('user_code')->get();
        $engineers = SqlHelper::select(
            'SELECT engineer_code, name, department FROM '.SqlHelper::table(SqlHelper::TABLE_ENGINEERS).' ORDER BY engineer_code'
        );

        return view('dashboard.adminUsers', [
            'clients' => $clients,
            'users' => $users,
            'engineers' => $engineers,
            'defaultPassword' => config('test.dummy_password'),
        ]);
    }

    public function storeClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_code' => 'required|string|max:20|regex:/^C[A-Z0-9]+$/i',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'username' => 'required|string|max:100|unique:portal_users,username',
            'password' => DatabaseSeeder::passwordRules(),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $clientCode = strtoupper($request->input('client_code'));

        DB::transaction(function () use ($request, $clientCode) {
            DB::table('clients')->updateOrInsert(
                ['client_code' => $clientCode],
                [
                    'client_code' => $clientCode,
                    'name' => $request->input('name'),
                    'erp_vertical' => 'TERMS',
                    'email' => $request->input('email'),
                ]
            );

            DB::table('portal_users')->updateOrInsert(
                ['user_code' => $clientCode],
                [
                    'user_code' => $clientCode,
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'status' => 'Y',
                ]
            );
        });

        return redirect()->route('admin.users')->with('success', "Client {$clientCode} and portal user created.");
    }

    public function storeSupportUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'engineer_code' => 'required|string|max:20|regex:/^S[A-Z0-9]+$/i',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:portal_users,username',
            'email' => 'nullable|email|max:255',
            'password' => DatabaseSeeder::passwordRules(),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $engineerCode = strtoupper($request->input('engineer_code'));

        DB::transaction(function () use ($request, $engineerCode) {
            DB::table('engineers')->updateOrInsert(
                ['engineer_code' => $engineerCode],
                [
                    'engineer_code' => $engineerCode,
                    'name' => $request->input('name'),
                    'working_status' => 'WK',
                    'department' => 'SWE',
                ]
            );

            DB::table('portal_users')->updateOrInsert(
                ['user_code' => $engineerCode],
                [
                    'user_code' => $engineerCode,
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'status' => 'Y',
                ]
            );
        });

        return redirect()->route('admin.users')->with('success', "Support user {$engineerCode} created.");
    }

    public function storePortalUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_code' => 'required|string|max:20',
            'username' => 'required|string|max:100|unique:portal_users,username',
            'email' => 'nullable|email|max:255',
            'password' => DatabaseSeeder::passwordRules(),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userCode = strtoupper($request->input('user_code'));

        if (! UserRole::isClient($userCode)) {
            return back()->withErrors(['user_code' => 'Portal user must be linked to a client code (not S* or A*).'])->withInput();
        }

        $clientExists = DB::table('clients')->where('client_code', $userCode)->exists();
        if (! $clientExists) {
            return back()->withErrors(['user_code' => 'Client code does not exist. Create the client first.'])->withInput();
        }

        DB::table('portal_users')->updateOrInsert(
            ['user_code' => $userCode],
            [
                'user_code' => $userCode,
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'status' => 'Y',
            ]
        );

        return redirect()->route('admin.users')->with('success', "Portal user for {$userCode} created.");
    }
}
