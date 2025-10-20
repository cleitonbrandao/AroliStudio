<div>
    <div>
        <h2 class="text-xl font-bold mb-4">Gerenciar Hierarquia</h2>
        @if(empty($companies) || count($companies) === 0)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                <strong>Atenção:</strong> Você não possui acesso a nenhuma empresa ou não foi adicionado a nenhuma equipe.<br>
                Caso acredite ser um erro, entre em contato com o administrador do sistema.
            </div>
        @else
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4">Empresas Relacionadas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($companies as $company)
                        <div class="bg-gradient-to-br from-blue-50 to-white shadow-lg rounded-xl p-6 border border-blue-100 flex flex-col gap-3 hover:shadow-2xl transition-shadow duration-200 ease-in-out">
                            <div class="flex items-center gap-3 mb-2 gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-200 text-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3v4M8 3v4m-5 4h18" /></svg>
                                    </span>
                                    <span class="text-xl font-bold text-blue-900">{{ $company->name }}</span>
                                </div>
                                <span class="ml-auto bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide shadow-sm border border-blue-200">
                                    {{ $roles[$company->membership->role ?? ''] ?? ($company->membership->role ?? 'Sem role') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600 text-sm mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span class="font-semibold">Data de ingresso:</span>
                                @if(isset($company->membership) && isset($company->membership->created_at))
                                    <span class="ml-1">{{ \Carbon\Carbon::parse($company->membership->created_at)->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="italic text-gray-400 ml-1">Desconhecida</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        </div>
</div>
