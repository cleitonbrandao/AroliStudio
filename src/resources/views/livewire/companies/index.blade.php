<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Minhas Empresas</h1>
                <a href="{{ route('companies.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Nova Empresa
                </a>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($companies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($companies as $company)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $company['name'] }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            Slug: {{ $company['slug'] }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Role: 
                                            <span class="font-semibold 
                                                @if($company['role'] === 'owner') text-red-600
                                                @elseif($company['role'] === 'admin') text-blue-600
                                                @else text-gray-600 @endif">
                                                {{ ucfirst($company['role']) }}
                                            </span>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Criada em: {{ $company['created_at']->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex space-x-2">
                                    <button wire:click="switchCompany({{ $company['id'] }})"
                                            class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded">
                                        Acessar
                                    </button>
                                    
                                    @if ($company['is_owner'])
                                        <button class="bg-yellow-500 hover:bg-yellow-700 text-white text-sm font-bold py-2 px-4 rounded">
                                            Gerenciar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma empresa</h3>
                    <p class="mt-1 text-sm text-gray-500">Comece criando sua primeira empresa.</p>
                    <div class="mt-6">
                        <a href="{{ route('companies.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Nova Empresa
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
