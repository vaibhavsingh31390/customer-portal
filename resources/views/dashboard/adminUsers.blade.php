@extends('base.base')

@section('title', 'User Management')

@section('content')
    @include('include.sidebarAdmin')

    @include('include.topNav')

    <div class="main-content d-flex flex-column portal-shell__main portal-main">
        <x-page-header title="User Management" subtitle="Create clients and portal users (test mode only)">
            <x-slot:actions>
                <x-button variant="ghost" :href="route('dashboard')" icon="home" class="portal-btn--icon-only" aria-label="Dashboard" />
            </x-slot:actions>
        </x-page-header>

        @if (session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        <div class="row">
            <div class="col-xl-4">
                <x-card title="Create Client + User">
                    <form action="{{ route('admin.clients.store') }}" method="POST" class="portal-form">
                        @csrf
                        <div class="form-group">
                            <label for="client_code">Client Code</label>
                            <input type="text" id="client_code" name="client_code" class="form-control" value="{{ old('client_code') }}" placeholder="C004" required>
                        </div>
                        <div class="form-group">
                            <label for="client_name">Client Name</label>
                            <input type="text" id="client_name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="client_email">Email</label>
                            <input type="email" id="client_email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label for="client_username">Username</label>
                            <input type="text" id="client_username" name="username" class="form-control" value="{{ old('username') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="client_password">Password</label>
                            <input type="text" id="client_password" name="password" class="form-control" value="{{ old('password', $defaultPassword) }}" required>
                        </div>
                        <x-button type="submit" variant="primary">Create Client</x-button>
                    </form>
                </x-card>
            </div>

            <div class="col-xl-4">
                <x-card title="Create Support User">
                    <form action="{{ route('admin.support.store') }}" method="POST" class="portal-form">
                        @csrf
                        <div class="form-group">
                            <label for="engineer_code">Engineer Code</label>
                            <input type="text" id="engineer_code" name="engineer_code" class="form-control" value="{{ old('engineer_code') }}" placeholder="S004" required>
                        </div>
                        <div class="form-group">
                            <label for="support_name">Name</label>
                            <input type="text" id="support_name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="support_email">Email</label>
                            <input type="email" id="support_email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label for="support_username">Username</label>
                            <input type="text" id="support_username" name="username" class="form-control" value="{{ old('username') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="support_password">Password</label>
                            <input type="text" id="support_password" name="password" class="form-control" value="{{ old('password', $defaultPassword) }}" required>
                        </div>
                        <x-button type="submit" variant="primary">Create Support User</x-button>
                    </form>
                </x-card>
            </div>

            <div class="col-xl-4">
                <x-card title="Add Portal User to Client">
                    <form action="{{ route('admin.portal.store') }}" method="POST" class="portal-form">
                        @csrf
                        <div class="form-group">
                            <label for="user_code">Client Code</label>
                            <select id="user_code" name="user_code" class="form-control" required>
                                <option value=""></option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->client_code }}" @selected(old('user_code') === $client->client_code)>
                                        {{ $client->client_code }} — {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="portal_username">Username</label>
                            <input type="text" id="portal_username" name="username" class="form-control" value="{{ old('username') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="portal_email">Email</label>
                            <input type="email" id="portal_email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label for="portal_password">Password</label>
                            <input type="text" id="portal_password" name="password" class="form-control" value="{{ old('password', $defaultPassword) }}" required>
                        </div>
                        <x-button type="submit" variant="primary">Create Portal User</x-button>
                    </form>
                </x-card>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <x-card title="Existing Portal Users">
                    <div class="table-responsive">
                        <table class="table portal-table">
                            <thead>
                                <tr>
                                    <th>User Code</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->user_code }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->status === 'Y' ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="portal-table-empty">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="flex-grow-1"></div>
        @include('include.footer')
    </div>
@endsection

@section('scripts')
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>showToast(@json($error), false);</script>
        @endforeach
    @endif
@endsection
