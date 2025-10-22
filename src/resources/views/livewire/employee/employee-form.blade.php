<div class="w-full">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-semibold text-gray-900 dark:text-white">
                {{ $isEditMode ? __('app.edit_employee') : __('app.create_employee') }}
            </h2>
            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                {{ $isEditMode ? __('app.edit_employee_description') : __('app.create_employee_description') }}
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg sm:rounded-xl">
            <form wire:submit="save" class="p-4 sm:p-6 lg:p-8">
                <div class="space-y-4 sm:space-y-6">
                    
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ __('app.name') }} <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            wire:model="name"
                            class="mt-1 block w-full rounded-lg shadow-sm text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2.5
                                @error('name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 
                                @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                @enderror"
                            placeholder="{{ __('app.enter_name') }}"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                <span class="font-medium">{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ __('app.email') }} <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            wire:model="email"
                            class="mt-1 block w-full rounded-lg shadow-sm text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2.5
                                @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 
                                @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                @enderror"
                            placeholder="{{ __('app.enter_email') }}"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                <span class="font-medium">{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label for="role" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ __('app.role') }} <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="role" 
                            wire:model="role"
                            class="mt-1 block w-full rounded-lg shadow-sm text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2.5
                                @error('role') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 
                                @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                @enderror"
                        >
                            @foreach($availableRoles as $roleKey => $roleLabel)
                                <option value="{{ $roleKey }}">{{ __('team-invitations.roles.' . $roleKey) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                <span class="font-medium">{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field (Optional for Edit) -->
                    <div>
                        <label for="password" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ __('app.password') }} 
                            @if(!$isEditMode)
                                <span class="text-red-500">*</span>
                            @else
                                <span class="text-xs sm:text-sm font-normal text-gray-500">({{ __('app.leave_blank_to_keep') }})</span>
                            @endif
                        </label>
                        <div class="relative mt-1">
                            <input 
                                type="{{ $showPassword ? 'text' : 'password' }}" 
                                id="password" 
                                wire:model="password"
                                class="block w-full rounded-lg shadow-sm pr-10 text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2.5
                                    @error('password') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 
                                    @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                    @enderror"
                                placeholder="{{ __('app.enter_password') }}"
                            >
                            <button 
                                type="button"
                                wire:click="togglePasswordVisibility"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                            >
                                @if($showPassword)
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                @endif
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                <span class="font-medium">{{ $message }}</span>
                            </p>
                        @enderror
                        @if(!$isEditMode || !empty($password))
                            <p class="mt-1.5 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                {{ __('app.password_requirements') }}
                            </p>
                        @endif
                    </div>

                    <!-- Password Confirmation Field -->
                    @if(!$isEditMode || !empty($password))
                        <div>
                            <label for="password_confirmation" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('app.confirm_password') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="{{ $showPassword ? 'text' : 'password' }}" 
                                id="password_confirmation" 
                                wire:model="password_confirmation"
                                class="mt-1 block w-full rounded-lg shadow-sm text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2.5
                                    @error('password_confirmation') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 
                                    @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                    @enderror"
                                placeholder="{{ __('app.confirm_password') }}"
                            >
                            @error('password_confirmation')
                                <p class="mt-1.5 text-xs sm:text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    @endif

                </div>

                <!-- Form Actions -->
                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4">
                    <button 
                        type="button"
                        wire:click="cancel"
                        class="w-full sm:w-auto order-2 sm:order-1 px-4 py-2.5 sm:py-2 text-sm sm:text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-150"
                    >
                        {{ __('app.cancel') }}
                    </button>
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="w-full sm:w-auto order-1 sm:order-2 inline-flex items-center justify-center px-4 py-2.5 sm:py-2 text-sm sm:text-base font-medium text-white bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                    >
                        <span wire:loading.remove wire:target="save">
                            {{ $isEditMode ? __('app.update') : __('app.create') }}
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('app.processing') }}...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
