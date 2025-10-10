@extends('layouts.dashboard.home')
@section('content')
    <div>
        <h2 class="text-xl font-bold mb-4">Gerenciar Hierarquia</h2>
        @if(empty($companies) || count($companies) === 0)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                <strong>Atenção:</strong> Você não possui acesso a nenhuma empresa ou não foi adicionado a nenhuma equipe.<br>
                Caso acredite ser um erro, entre em contato com o administrador do sistema.
            </div>
        @else
            <div class="mb-4">
                <label for="company" class="block font-semibold mb-1">Selecione a empresa/filial:</label>
                <select wire:model="selectedCompany" id="company" class="border rounded px-2 py-1">
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <h3 class="font-semibold mb-2">Membros</h3>
                <table class="min-w-full border">
                    <thead>
                        <tr>
                            <th class="border px-2 py-1">Nome</th>
                            <th class="border px-2 py-1">Email</th>
                            <th class="border px-2 py-1">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            <tr>
                                <td class="border px-2 py-1">{{ $member->name }}</td>
                                <td class="border px-2 py-1">{{ $member->email }}</td>
                                <td class="border px-2 py-1">
                                    @if(isset($member->pivot) && isset($member->pivot->role))
                                        {{ $roles[$member->pivot->role] ?? $member->pivot->role }}
                                    @else
                                        <span class="text-gray-400 italic">Sem role</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-2">Nenhum membro encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
