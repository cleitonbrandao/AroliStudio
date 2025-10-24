<?php

namespace App\Services;

use App\Models\Costumer;
use App\Models\People;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * Listar customers com filtros e paginação
     */
    public function list(
        ?string $search = null,
        int $perPage = 15,
        ?Team $team = null
    ): LengthAwarePaginator {
        $query = Costumer::with('people', 'team')
            ->auth();

        if ($search) {
            $query->search($search);
        }

        if ($team) {
            $query->where('team_id', $team->id);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Buscar customer por ID
     */
    public function find(int $customerId): ?Costumer
    {
        return Costumer::with('people', 'team')
            ->auth()
            ->find($customerId);
    }

    /**
     * Criar novo customer com pessoa
     */
    public function create(array $data, Team $team): Costumer
    {
        return DB::transaction(function () use ($data, $team) {
            // Criar pessoa primeiro
            $person = People::create([
                'team_id' => $team->id,
                'name' => $data['name'],
                'last_name' => $data['last_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'photo' => $data['photo'] ?? null,
            ]);

            // Criar customer vinculado à pessoa
            $customer = Costumer::create([
                'team_id' => $team->id,
                'person_id' => $person->id,
                'cpf' => $data['cpf'] ?? null,
                'email' => $data['email'],
                'birthday' => $data['birthday'] ?? null,
            ]);

            return $customer->load('people', 'team');
        });
    }

    /**
     * Atualizar customer existente
     */
    public function update(Costumer $customer, array $data): Costumer
    {
        return DB::transaction(function () use ($customer, $data) {
            // Atualizar dados da pessoa
            if ($customer->people) {
                $customer->people->update([
                    'name' => $data['name'] ?? $customer->people->name,
                    'last_name' => $data['last_name'] ?? $customer->people->last_name,
                    'phone' => $data['phone'] ?? $customer->people->phone,
                    'photo' => $data['photo'] ?? $customer->people->photo,
                ]);
            }

            // Atualizar dados do customer
            $customer->update([
                'cpf' => $data['cpf'] ?? $customer->cpf,
                'email' => $data['email'] ?? $customer->email,
                'birthday' => $data['birthday'] ?? $customer->birthday,
            ]);

            return $customer->fresh(['people', 'team']);
        });
    }

    /**
     * Deletar customer (e opcionalmente a pessoa)
     */
    public function delete(Costumer $customer, bool $deletePerson = false): bool
    {
        return DB::transaction(function () use ($customer, $deletePerson) {
            $person = $customer->people;
            
            // Deletar customer
            $customer->delete();

            // Se solicitado, deletar a pessoa também
            if ($deletePerson && $person) {
                $person->delete();
            }

            return true;
        });
    }

    /**
     * Buscar customers por CPF
     */
    public function findByCpf(string $cpf, ?Team $team = null): ?Costumer
    {
        $query = Costumer::with('people', 'team')
            ->where('cpf', $cpf);

        if ($team) {
            $query->where('team_id', $team->id);
        } else {
            $query->auth();
        }

        return $query->first();
    }

    /**
     * Buscar customers por email
     */
    public function findByEmail(string $email, ?Team $team = null): ?Costumer
    {
        $query = Costumer::with('people', 'team')
            ->where('email', $email);

        if ($team) {
            $query->where('team_id', $team->id);
        } else {
            $query->auth();
        }

        return $query->first();
    }

    /**
     * Verificar se CPF já existe
     */
    public function cpfExists(string $cpf, ?int $excludeCustomerId = null, ?Team $team = null): bool
    {
        $query = Costumer::where('cpf', $cpf);

        if ($excludeCustomerId) {
            $query->where('id', '!=', $excludeCustomerId);
        }

        if ($team) {
            $query->where('team_id', $team->id);
        } else {
            $query->auth();
        }

        return $query->exists();
    }

    /**
     * Verificar se email já existe
     */
    public function emailExists(string $email, ?int $excludeCustomerId = null, ?Team $team = null): bool
    {
        $query = Costumer::where('email', $email);

        if ($excludeCustomerId) {
            $query->where('id', '!=', $excludeCustomerId);
        }

        if ($team) {
            $query->where('team_id', $team->id);
        } else {
            $query->auth();
        }

        return $query->exists();
    }

    /**
     * Obter estatísticas de customers
     */
    public function getStatistics(?Team $team = null): array
    {
        $query = Costumer::auth();

        if ($team) {
            $query->where('team_id', $team->id);
        }

        $total = $query->count();
        $withCpf = (clone $query)->whereNotNull('cpf')->count();
        $withEmail = (clone $query)->whereNotNull('email')->count();
        $withBirthday = (clone $query)->whereNotNull('birthday')->count();

        return [
            'total' => $total,
            'with_cpf' => $withCpf,
            'with_email' => $withEmail,
            'with_birthday' => $withBirthday,
            'complete_percentage' => $total > 0 ? round(($withCpf / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Obter customers recentes
     */
    public function getRecent(int $limit = 10, ?Team $team = null): Collection
    {
        $query = Costumer::with('people', 'team')
            ->auth()
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($team) {
            $query->where('team_id', $team->id);
        }

        return $query->get();
    }
}
