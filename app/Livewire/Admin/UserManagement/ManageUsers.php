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
        'name.required' => 'El nombre completo es obligatorio.',
        'name.string' => 'El nombre debe ser un texto válido.',
        'name.max' => 'El nombre es demasiado largo (máximo 255 caracteres).',

        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El formato del correo electrónico no es válido.',
        'email.max' => 'El correo es demasiado largo.',
        'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',

        'password.required' => 'La contraseña es obligatoria para nuevos usuarios.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

        'role.required' => 'Debes seleccionar un rol para el usuario.',
        'role.in' => 'El rol seleccionado no es válido.',

        'userColegios.*.colegio_id.required' => 'Es necesario seleccionar un colegio en la lista.',
        'userColegios.*.colegio_id.exists' => 'El colegio seleccionado no es válido.',
        'userColegios.*.colegio_id.distinct' => 'No puedes asignar el mismo colegio dos veces.',

        'userColegios.*.role.required' => 'Debes definir qué cargo ocupará en este colegio.',
        'userColegios.*.role.string' => 'El cargo debe ser texto.',
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
