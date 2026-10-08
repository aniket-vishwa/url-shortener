<x-app-layout>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Short URLs</h2>

        @if (!auth()->user()->isSuperAdmin())
            @if ($errors->any())
                <ul class="text-red-600 mb-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('short-urls.store') }}" class="mb-6">
                @csrf
                <input type="url" name="original_url" placeholder="https://example.com/long-link" class="border w-96" value="{{ old('original_url') }}">
                <button type="submit" class="border px-3 py-1">Shorten</button>
            </form>
        @endif

        <table class="border">
            <tr>
                <th class="border px-2">Short URL</th>
                <th class="border px-2">Original URL</th>
                <th class="border px-2">Created by</th>
                <th class="border px-2">Created at</th>
            </tr>
            @forelse ($urls as $url)
                <tr>
                    <td class="border px-2">
                        <a href="{{ url('/s/'.$url->short_code) }}">{{ url('/s/'.$url->short_code) }}</a>
                    </td>
                    <td class="border px-2">{{ $url->original_url }}</td>
                    <td class="border px-2">{{ $url->user->name ?? '-' }}</td>
                    <td class="border px-2">{{ $url->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-2">No short URLs yet.</td></tr>
            @endforelse
        </table>
    </div>
</x-app-layout>