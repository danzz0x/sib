<div class="flex flex-col gap-10 bg-white" x-data="{ isEdit: false }" :class="{ 'modo-edicion-activo': isEdit }">
    <x-colegio.colegio-presentacion :colegio="$colegio" :can-manage-post="$this->canManageContent" />
    <x-colegio.secciones :secciones="$secciones" :colegio="$colegio" :can-manage-post="$this->canManageContent" :aniosDisponibles="$aniosDisponibles" :anioSeleccionado="$anioSeleccionado" />
    <x-colegio.footer :colegio="$colegio" />
</div>
