<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 text-center">
        For your security, please confirm your password to continue using Interlink internships and placements.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="'Password'" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">Back to login</a>
            <x-primary-button>
                Confirm
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
