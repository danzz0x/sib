<div class="flex flex-col gap-10 bg-white">
    <x-colegio.colegio-presentacion :colegio="$colegio" />
    <x-colegio.secciones :secciones="$secciones" :colegio="$colegio" :aniosDisponibles="$aniosDisponibles" :anioSeleccionado="$anioSeleccionado" />
    <x-colegio.footer :colegio="$colegio" />
</div>
