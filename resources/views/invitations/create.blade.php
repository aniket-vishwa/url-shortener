<x-app-layout>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Invite User</h2>

        @if (session('invite_link'))
            <p class="mb-4">Invitation link: <br>
                <a href="{{ session('invite_link') }}">{{ session('invite_link') }}</a>
            </p>
        @endif

        @if ($errors->any())
            <ul class="text-red-600 mb-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('invitations.store') }}">
            @csrf

            @if (auth()->user()->isSuperAdmin())
                <p>Company name:</p>
                <input type="text" name="company_name" value="{{ old('company_name') }}" class="border mb-3"><br>
            @endif

            <p>Email:</p>
            <input type="email" name="email" value="{{ old('email') }}" class="border mb-3"><br>

            @if (auth()->user()->isAdmin())
                <p>Role:</p>
                <select name="role" class="border mb-3">
                    <option value="Member">Member</option>
                    <option value="Admin">Admin</option>
                </select><br>
            @endif

            <button type="submit" class="border px-3 py-1">Send Invite</button>
        </form>
    </div>
</x-app-layout>