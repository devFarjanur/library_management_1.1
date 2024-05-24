<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Title -->
        <h2 class="text-xl font-semibold mt-4 mb-4 text-center">Library Membership Registration</h2>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- ID -->
        <div class="mt-4">
            <x-input-label for="student_id" :value="__('Student_id')" />
            <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id')" required
                autofocus autocomplete="student_id" />
            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required
                autocomplete="phone" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Age -->
        <div class="mt-4">
            <x-input-label for="age" :value="__('Age')" />
            <x-text-input id="age" class="block mt-1 w-full" type="text" name="age" :value="old('age')" required
                autofocus autocomplete="age" />
            <x-input-error :messages="$errors->get('age')" class="mt-2" />
        </div>

        <!-- Gender -->
        <div class="mt-4">
            <x-input-label for="gender" :value="__('Gender')" />
            <x-text-input id="gender" class="block mt-1 w-full" type="text" name="gender" :value="old('gender')" required
                autofocus autocomplete="gender" />
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>


        <!-- Address -->
        <div class="mt-4">
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required
                autofocus autocomplete="address" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>



        <!-- Payment Method -->
        <div class="mt-4">
            <x-input-label for="payment_id" :value="__('Payment Method')" />
            <select id="payment_id" name="payment_method" class="block mt-1 w-full rounded-md" required>
                <option value="" selected disabled>-- Select Payment Method --</option>
                @foreach($payments as $payment)
                    <option value="{{ $payment->id }}">{{ $payment->payment_method_name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
        </div>

        <!-- Transaction ID -->
        <div class="mt-4">
            <x-input-label for="trx_id" :value="__('Transaction ID')" />
            <x-text-input id="trx_id" class="block mt-1 w-full" type="text" name="trx_id" :value="old('trx_id')"
                required />
            <x-input-error :messages="$errors->get('trx_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>