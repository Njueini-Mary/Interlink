<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        Thank you for registering with Interlink! Please verify your email address by clicking the link we just sent. This helps keep your internships and placements secure.
        <br>If you didn't receive the email, we will gladly send you another.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 text-center">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                Resend Verification Email
            </x-primary-button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
