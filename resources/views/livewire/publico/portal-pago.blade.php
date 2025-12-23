<div x-data="{ open: false }" @close-modal.window="open = false" class="z-40 font-sans">

    <button @click="open = true"
        class="fixed bottom-8 right-8 z-40 flex items-center gap-3 bg-slate-900 hover:bg-slate-800 text-white px-6 py-3.5 rounded-full shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl group border border-slate-700"
        title="Realizar Aporte">
        <div class="bg-white/10 p-1.5 rounded-full group-hover:bg-white/20 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <span class="font-semibold tracking-wide text-sm">Pagar Aporte</span>
    </button>

    <template x-teleport="body">
        <div x-show="open" style="display: none;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">

            <div class="fixed inset-0 bg-slate-950/90 transition-opacity" @click="open = false"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-5xl border border-gray-100">

                    <div
                        class="border-b border-gray-100 px-6 py-4 flex items-center justify-between bg-white sticky top-0 z-10">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-lg bg-slate-50 flex items-center justify-center border border-slate-100">
                                <svg class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Portal de Pagos</h3>
                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $colegioId ? 'Sección Especializada' : 'SIB Nacional' }}
                                </p>
                            </div>
                        </div>
                        <button @click="open = false"
                            class="text-slate-400 hover:text-slate-600 transition p-2 rounded-full hover:bg-slate-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div wire:loading wire:target="buscarSocio, procesarPago, updatedFechaInicio, updatedFechaFin"
                        class="absolute inset-0 bg-white/90 z-50 flex items-center justify-center">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-10 h-10 border-4 border-slate-200 border-t-slate-800 rounded-full animate-spin">
                            </div>
                            <span class="text-slate-600 font-medium text-sm mt-3 animate-pulse">Procesando...</span>
                        </div>
                    </div>

                    <div class="px-6 py-6 sm:p-8">

                        @if ($paso === 1)
                            <div class="max-w-md mx-auto">
                                <form wire:submit.prevent="buscarSocio" class="space-y-6">
                                    <div class="text-center mb-8">
                                        <h2 class="text-2xl font-bold text-slate-900">Identifícate</h2>
                                        <p class="text-slate-500 mt-2">Ingresa tu número de registro o documento para
                                            continuar.</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wide">RNI
                                            o Cédula</label>
                                        <input type="text" wire:model="rni"
                                            class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-800 focus:ring-slate-800 text-lg py-3 pl-4 bg-slate-50 focus:bg-white transition-colors placeholder:text-slate-400"
                                            placeholder="Ej: 1234567">
                                        @error('rni')
                                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1"><svg
                                                    class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex justify-center py-2" wire:ignore>
                                        <div x-data x-init="let load = () => {
                                            if (window.turnstile) {
                                                $el.innerHTML = '';
                                                turnstile.render($el, {
                                                    sitekey: '{{ config('services.turnstile.key') }}',
                                                    theme: 'light',
                                                    callback: (t) => $wire.set('turnstileToken', t),
                                                    'expired-callback': () => $wire.set('turnstileToken', null)
                                                });
                                            } else setTimeout(load, 100);
                                        };
                                        load();"></div>
                                    </div>
                                    @error('turnstileToken')
                                        <p class="text-center text-sm text-red-600 font-medium">{{ $message }}</p>
                                    @enderror

                                    <button type="submit"
                                        class="w-full bg-slate-900 text-white font-bold py-3.5 px-4 rounded-lg shadow-lg hover:bg-slate-800 transition-all transform active:scale-[0.98]">
                                        Continuar
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($paso === 2)
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 text-left">

                                <div class="space-y-6">
                                    <div
                                        class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div
                                            class="h-12 w-12 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-lg shadow-sm">
                                            {{ substr($socio->nombre, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-slate-900">{{ $socio->nombre }}</h3>
                                            <p class="text-sm text-slate-500">{{ $socio->tipoSocio->nombre }} •
                                                {{ $socio->colegio->nombre }}</p>
                                        </div>
                                        <button wire:click="resetear"
                                            class="text-xs font-bold text-slate-400 hover:text-slate-800 uppercase tracking-wide">Cambiar</button>
                                    </div>

                                    @if ($cuentaBancaria)
                                        <div
                                            class="bg-slate-900 rounded-2xl p-6 text-white shadow-2xl relative overflow-hidden text-center group">

                                            <div class="mb-6 flex justify-center items-center gap-2 opacity-80">
                                                <svg class="w-5 h-5 text-emerald-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 19h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                <span class="text-sm font-semibold tracking-widest uppercase">Escanea
                                                    con tu Banco</span>
                                            </div>

                                            <div class="relative inline-block mx-auto mb-6">
                                                <div
                                                    class="absolute -inset-1 bg-gradient-to-tr from-emerald-500 to-cyan-500 rounded-xl opacity-50 blur-lg group-hover:opacity-80 transition duration-500">
                                                </div>

                                                <div class="relative bg-white p-3 rounded-xl shadow-inner">
                                                    @if ($cuentaBancaria->qr_path)
                                                        <img src="{{ asset('storage/' . $cuentaBancaria->qr_path) }}"
                                                            class="w-56 h-56 object-cover rounded-lg border border-slate-100">
                                                    @else
                                                        <div
                                                            class="w-56 h-56 flex items-center justify-center bg-slate-50 text-slate-300 border-2 border-dashed border-slate-200 rounded-lg">
                                                            Sin QR
                                                        </div>
                                                    @endif

                                                    <div
                                                        class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-slate-900">
                                                    </div>
                                                    <div
                                                        class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-slate-900">
                                                    </div>
                                                    <div
                                                        class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-slate-900">
                                                    </div>
                                                    <div
                                                        class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-slate-900">
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/10 mx-auto max-w-[280px]">
                                                <p
                                                    class="text-[10px] text-slate-300 uppercase font-bold tracking-widest mb-1">
                                                    Monto Exacto a Transferir</p>
                                                <div class="flex items-baseline justify-center gap-1">
                                                    <span
                                                        class="text-4xl font-black text-white tracking-tight">{{ $monto > 0 ? $monto : '0.00' }}</span>
                                                    <span class="text-lg font-bold text-emerald-400">Bs</span>
                                                </div>
                                            </div>

                                            <div class="mt-4 pt-4 border-t border-white/10 text-slate-400 text-xs">
                                                <p>{{ $cuentaBancaria->banco }} • {{ $cuentaBancaria->nro_cuenta }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="p-4 bg-amber-50 text-amber-800 rounded-lg text-sm border border-amber-200">
                                            Sin cuenta bancaria activa.</div>
                                    @endif
                                </div>

                                <form wire:submit.prevent="procesarPago" class="space-y-6">

                                    <div class="bg-white p-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                            <span
                                                class="bg-green-100 text-green-700 w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span>
                                            Detalles del Pago
                                        </h3>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase mb-1">Desde
                                                    el mes</label>
                                                <div class="relative">
                                                    <select wire:model.live="fecha_inicio"
                                                        class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:border-green-500 focus:ring-green-500 sm:text-sm py-2.5">
                                                        @foreach ($listaMeses as $fecha => $nombre)
                                                            <option value="{{ $fecha }}">{{ $nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase mb-1">Hasta
                                                    el mes</label>
                                                <div class="relative">
                                                    <select wire:model.live="fecha_fin"
                                                        class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:border-green-500 focus:ring-green-500 sm:text-sm py-2.5">
                                                        @foreach ($listaMeses as $fecha => $nombre)
                                                            @if ($fecha >= $fecha_inicio)
                                                                <option value="{{ $fecha }}">
                                                                    {{ $nombre }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-4 bg-green-50 rounded-lg p-4 border border-green-100 flex justify-between items-center">
                                            <div>
                                                <p class="text-xs text-green-600 font-bold uppercase">Resumen</p>
                                                <p class="text-sm text-green-800">Pagando <span
                                                        class="font-bold">{{ $cantidadMeses }} mes(es)</span> de
                                                    aporte.</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-green-600 font-bold uppercase">Total a
                                                    Transferir</p>
                                                <p class="text-2xl font-extrabold text-green-700">{{ $monto }}
                                                    <span class="text-sm font-medium">Bs</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-100 pt-4">
                                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                            <span
                                                class="bg-green-100 text-green-700 w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span>
                                            Comprobante
                                        </h3>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">ID de
                                                    Transacción / Nro. Comprobante</label>
                                                <input type="text" wire:model="nro_transaccion"
                                                    placeholder="Ej: 12345678"
                                                    class="block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 sm:text-sm py-2.5">
                                                @error('nro_transaccion')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Subir Foto
                                                    / Captura</label>
                                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition cursor-pointer relative"
                                                    onclick="document.getElementById('file-upload').click()">

                                                    <div class="space-y-1 text-center">
                                                        @if ($comprobante)
                                                            <img src="{{ $comprobante->temporaryUrl() }}"
                                                                class="mx-auto h-24 rounded shadow-sm">
                                                            <p class="text-xs text-green-600 font-bold mt-2">¡Imagen
                                                                cargada!</p>
                                                            <p class="text-xs text-gray-400">Clic para cambiar</p>
                                                        @else
                                                            <svg class="mx-auto h-12 w-12 text-gray-400"
                                                                stroke="currentColor" fill="none"
                                                                viewBox="0 0 48 48">
                                                                <path
                                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                            <div class="flex text-sm text-gray-600 justify-center">
                                                                <span
                                                                    class="relative bg-white rounded-md font-medium text-green-600 hover:text-green-500">Subir
                                                                    un archivo</span>
                                                            </div>
                                                            <p class="text-xs text-gray-500">PNG, JPG hasta 5MB</p>
                                                        @endif
                                                        <input id="file-upload" wire:model="comprobante"
                                                            type="file" class="sr-only" accept="image/*">
                                                    </div>
                                                </div>
                                                @error('comprobante')
                                                    <span
                                                        class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                                        <h4
                                            class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            Datos de Envío
                                        </h4>

                                        @if ($pedir_telefono)
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">Registrar
                                                    Celular <span class="text-red-500">*</span></label>
                                                <input type="text" wire:model="nuevo_telefono"
                                                    placeholder="Ej: 77712345"
                                                    class="w-full rounded-lg border-amber-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 bg-amber-50/50 placeholder-slate-400 text-sm">
                                                @error('nuevo_telefono')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-sm">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="text-slate-600">Recibo al WhatsApp:</span>
                                                <span class="font-bold text-slate-900">{{ $socio->telefono }}</span>
                                            </div>
                                        @endif

                                        @if ($pedir_email)
                                            <div class="pt-2 border-t border-slate-200">
                                                <label class="block text-xs font-medium text-slate-600 mb-1">Registrar
                                                    Correo (Opcional)</label>
                                                <input type="email" wire:model="nuevo_email"
                                                    placeholder="correo@ejemplo.com"
                                                    class="w-full rounded-lg border-slate-300 focus:border-slate-800 focus:ring-slate-800 shadow-sm py-2 text-sm">
                                                @error('nuevo_email')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            <div
                                                class="flex items-center gap-2 text-sm pt-2 border-t border-slate-200">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span class="text-slate-600">Copia al correo:</span>
                                                <span
                                                    class="font-bold text-slate-900 truncate max-w-[150px]">{{ $socio->email }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <button type="submit"
                                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-[1.02]">
                                        CONFIRMAR Y ENVIAR PAGO
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($paso === 3)
                            <div class="text-center py-12 max-w-sm mx-auto">
                                <div
                                    class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-emerald-100 mb-6">
                                    <svg class="h-12 w-12 text-emerald-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-900 mb-2">¡Pago Recibido!</h3>
                                <p class="text-slate-500 mb-8 leading-relaxed">Hemos recibido tu comprobante
                                    correctamente. Nuestro equipo lo verificará en breve.</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <button wire:click="resetear"
                                        class="w-full py-3 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">Nuevo
                                        Pago</button>
                                    <button wire:click="cerrarModal"
                                        class="w-full py-3 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">Finalizar</button>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
