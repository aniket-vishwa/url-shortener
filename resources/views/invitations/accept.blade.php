<x-guest-layout>
    <h2 class="text-xl font-bold mb-4">Accept Invitation</h2>
    <p class="mb-4">Email: {{ $invitation->email }}</p>

    @if ($errors->any())
        <ul class="text-red-600 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('invitations.complete', $invitation->token) }}">
        @csrf
        <p>Name:</p>
        <input type="text" name="name" class="border mb-3"><br>

        <p>Password:</p>
        <input type="password" name="password" class="border mb-3"><br>

        <p>Confirm password:</p>
        <input type="password" name="password_confirmation" class="border mb-3"><br>

        <button type="submit" class="border px-3 py-1">Create Account</button>
    </form>
</x-guest-layout>