<div>
    @if (!$this->canManageMembers)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        {{ __('app.no_permission_view_members') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="flex justify-between items-center mb-4">
            <div class="relative flex-1 max-w-md">
                <label for="table-search" class="sr-only">{{ __('app.search') }}</label>
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="table-search-users" 
                    wire:model.live.debounce.300ms="search"
                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                    placeholder="{{ __('app.search_members') }}"
                >
            </div>
            
            <a href="{{ route('root.employee.create') }}" 
                class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('app.create_employee') }}
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                {{ __('app.name') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                {{ __('app.role') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                {{ __('app.email') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                {{ __('app.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            @php
                                $memberRole = $this->getMemberRole($member);
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                    <img class="w-10 h-10 rounded-full" src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}">
                                    <div class="pl-3">
                                        <div class="text-base font-semibold">{{ $member->name }}</div>
                                    </div>
                                </th>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($memberRole === 'owner') bg-purple-100 text-purple-800
                                        @elseif($memberRole === 'admin') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $this->getRoleLabel($memberRole) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $member->email }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($member->id !== auth()->id())
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('root.employee.edit', ['userId' => $member->id]) }}" 
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                                {{ __('app.edit') }}
                                            </a>
                                            <button
                                                x-data
                                                @click="$dispatch('open-remove-modal', { userId: {{ $member->id }}, userName: '{{ $member->name }}' })"
                                                class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                                {{ __('app.remove') }}
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">{{ __('app.you') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    @if($search)
                                        {{ __('app.no_members_found') }}
                                    @else
                                        {{ __('app.no_members_yet') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($members->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Remove Confirmation Modal -->
    <div x-data="{ 
            show: false, 
            userId: null, 
            userName: '' 
         }"
         @open-remove-modal.window="
            userId = $event.detail.userId;
            userName = $event.detail.userName;
            show = true;
         "
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="show = false"></div>

        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6"
                 @click.away="show = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                
                <!-- Icon -->
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <!-- Content -->
                <div class="mt-4 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('app.confirm_remove_employee') }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('app.remove_employee_warning') }}
                        </p>
                        <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300" x-text="userName"></p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button 
                        type="button"
                        @click="show = false"
                        class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        {{ __('app.cancel') }}
                    </button>
                    <button 
                        type="button"
                        @click="$wire.removeMember(userId); show = false"
                        class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        {{ __('app.remove') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
