<div class="w-full">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ $isEditMode ? 'Editar Cliente' : 'Novo Cliente' }}
                    </h2>
                    <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        {{ $isEditMode ? 'Atualize as informações do cliente abaixo.' : 'Preencha os dados do novo cliente.' }}
                    </p>
                </div>
                
                <!-- Botão Voltar -->
                <button 
                    type="button" 
                    wire:click="cancel"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar
                </button>
            </div>
        </div>

        <!-- Mensagens de Feedback -->
        @if (session('success'))
            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Sucesso!</span> {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Erro!</span> {{ session('error') }}
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-4 p-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Info:</span> {{ session('info') }}
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg sm:rounded-xl">
            <form wire:submit="save" class="p-4 sm:p-6 lg:p-8">
                <div class="space-y-6">
                    
                    <!-- Dados Pessoais Section -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                            Dados Pessoais
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- Nome -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nome <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    wire:model="form.name"
                                    class="block w-full rounded-lg shadow-sm text-sm px-4 py-2.5
                                        @error('form.name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 
                                        @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                        @enderror"
                                    placeholder="Digite o nome"
                                    maxlength="45"
                                >
                                @error('form.name')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                                        <span class="font-medium">{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            <!-- Sobrenome -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Sobrenome
                                </label>
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    wire:model="form.last_name"
                                    class="block w-full rounded-lg shadow-sm text-sm px-4 py-2.5 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Digite o sobrenome"
                                    maxlength="60"
                                >
                                @error('form.last_name')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                                        <span class="font-medium">{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Telefone
                                </label>
                                <input 
                                    type="text" 
                                    id="phone" 
                                    wire:model="form.phone"
                                    class="block w-full rounded-lg shadow-sm text-sm px-4 py-2.5 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="(00) 00000-0000"
                                    maxlength="20"
                                    x-mask="(99) 99999-9999"
                                >
                                @error('form.phone')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                                        <span class="font-medium">{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            <!-- Data de Nascimento -->
                            <div>
                                <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Data de Nascimento
                                </label>
                                <input 
                                    type="date" 
                                    id="birthday" 
                                    wire:model="form.birthday"
                                    class="block w-full rounded-lg shadow-sm text-sm px-4 py-2.5 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                    max="{{ date('Y-m-d') }}"
                                >
                                @error('form.birthday')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                                        <span class="font-medium">{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Documentos e Contato Section -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                            Documentos e Contato
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <!-- CPF -->
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    CPF
                                </label>
                                <input 
                                    type="text" 
                                    id="cpf" 
                                    wire:model="form.cpf"
                                    class="block w-full rounded-lg shadow-sm text-sm px-4 py-2.5
                                        @error('form.cpf') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 
                                        @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                        @enderror"
                                    placeholder="000.000.000-00"
                                    maxlength="11"
                                    x-mask="999.999.999-99"
                                >
                                @error('form.cpf')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                                        <span class="font-medium">{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    E-mail <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    wire:model="form.email"
                                    class="block w-full rounded-lg shadow-sm text-sm px-4 py-2.5
                                        @error('form.email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 
                                        @else border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 
                                        @enderror"
                                    placeholder="exemplo@email.com"
                                    maxlength="255"
                                >
                                @error('form.email')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                                        <span class="font-medium">{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        
                        <div class="flex items-center gap-3">
                            <!-- Botão Limpar (somente no create) -->
                            @if(!$isEditMode)
                                <button 
                                    type="button"
                                    wire:click="clear"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Limpar
                                </button>
                            @endif

                            <!-- Botão Deletar (somente no edit) -->
                            @if($isEditMode)
                                <button 
                                    type="button"
                                    wire:click="confirmDelete"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    wire:loading.attr="disabled"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Excluir
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Botão Cancelar -->
                            <button 
                                type="button"
                                wire:click="cancel"
                                class="inline-flex items-center px-6 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                Cancelar
                            </button>

                            <!-- Botão Salvar -->
                            <button 
                                type="submit"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                            >
                                <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="save">
                                    {{ $isEditMode ? 'Atualizar' : 'Cadastrar' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    Salvando...
                                </span>
                            </button>
                        </div>

                    </div>

                </div>
            </form>
        </div>

    </div>

    <!-- Modal de Confirmação de Exclusão -->
    @if($showDeleteConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="cancelDelete"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Confirmar Exclusão
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button 
                            type="button"
                            wire:click="delete"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="delete">Sim, excluir</span>
                            <span wire:loading wire:target="delete">Excluindo...</span>
                        </button>
                        <button 
                            type="button"
                            wire:click="cancelDelete"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
