<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Colegio;
use App\Models\User;
use App\Traits\WithAlerts;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ManageUsers extends Component
{
    use WithAlerts;

    public $userId;

    public $name;

    public $email;

    public $password;

    public $role;

    public $colegiosOptions;

    public $userColegios = [];

    public $openModal = false;

    const ROLES_COLEGIO = ['director', 'tesorero', 'publicador'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => [Rule::requiredIf(empty($this->userId)), 'nullable', 'string', 'min:8'],
            'role' => 'required|in:'.User::ROLE_ADMIN.','.User::ROLE_COLEGIO.','.User::ROLE_CAJERO,
        ];

        if ($this->role === User::ROLE_COLEGIO) {
            $rules['userColegios.*.colegio_id'] = 'required|exists:colegios,id|distinct';
            $rules['userColegios.*.role'] = 'required|string';
        }

        return $rules;
    }

    protected $messages = [
        'userColegios.*.colegio_id.required' => 'Seleccione un colegio.',
        'userColegios.*.colegio_id.distinct' => 'No puedes seleccionar el mismo colegio dos veces.',
        'userColegios.*.role.required' => 'Seleccione un rol para el colegio.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
    ];

    public function mount()
    {
        $this->colegiosOptions = Colegio::select(['id', 'nombre'])->orderBy('nombre')->get();
    }

    public function render()
    {
        // Ordenamos para que los inactivos se vayan al final visualmente o como prefieras
        $users = User::with('colegios')
            ->orderBy('activo', 'desc') // Activos primero
            ->latest()
            ->get();

        return view('livewire.admin.user-management.manage-users', compact('users'));
    }

    // ... (Métodos addColegio, removeColegio, create, edit, resetFields siguen igual) ...
    public function addColegio()
    {
        $this->userColegios[] = ['colegio_id' => '', 'role' => ''];
    }

    public function removeColegio($index)
    {
        unset($this->userColegios[$index]);
        $this->userColegios = array_values($this->userColegios);
    }

    public function resetFields()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'role', 'userColegios']);
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetFields();
        $this->addColegio();
        $this->openModal = true;
    }

    public function edit($id)
    {
        $user = User::with('colegios')->findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = null;

        $this->userColegios = $user->colegios->map(function ($colegio) {
            return [
                'colegio_id' => $colegio->id,
                'role' => $colegio->pivot->tipo_usuario_colegio,
            ];
        })->toArray();

        if (empty($this->userColegios) && $this->role === User::ROLE_COLEGIO) {
            $this->addColegio();
        }

        $this->openModal = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => trim($this->name),
            'email' => trim($this->email),
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        try {
            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->update($data);
                $this->toastUpdated('Usuario');
            } else {
                $user = User::create($data);
                $this->toastCreated('Usuario');
            }

            if ($this->role === User::ROLE_COLEGIO) {
                $syncData = [];
                foreach ($this->userColegios as $item) {
                    if (! empty($item['colegio_id']) && ! empty($item['role'])) {
                        $syncData[$item['colegio_id']] = ['tipo_usuario_colegio' => $item['role']];
                    }
                }
                $user->colegios()->sync($syncData);
            } else {
                $user->colegios()->detach();
            }

            $this->openModal = false;
        } catch (\Exception $e) {
            $this->toastError('Error al guardar: '.$e->getMessage());
        }
    }

    public function updatedRole($value)
    {
        if ($value === User::ROLE_COLEGIO) {
            if (empty($this->userColegios)) {
                $this->addColegio();
            }
        } else {
            $this->userColegios = [];
        }
    }

    public function toggleStatus($id)
    {
        if ($id === auth()->id()) {
            $this->toastError('No puedes desactivar tu propia cuenta.');

            return;
        }

        try {
            $user = User::findOrFail($id);
            $user->activo = ! $user->activo;
            $user->save();

            if ($user->activo) {
                $this->toastActivated('Usuario');
            } else {
                $this->toastDeactivated('Usuario');
            }

        } catch (\Exception $e) {
            $this->toastError('No se pudo cambiar el estado del usuario.');
        }
    }
}
