<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Critical optimizations -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    
    <!-- Fonts críticos con display=swap -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    
    <!-- CSS crítico -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')
        
        <!-- Page Content -->
        <main class="pt-16">
            {{ $slot }}
        </main>
    </div>
    
    @stack('modals')
    
    <!-- Scripts base -->
    @livewireScripts
    
    <script>
        // Variables globales para prevenir redeclaración
        window.globalSystemLoaded = window.globalSystemLoaded || false;
        window.utilsLoaded = window.utilsLoaded || false;

        // Utilidades globales (imágenes y debounce)
        if (!window.utilsLoaded) {
            window.utilsLoaded = true;
            
            // Optimización de imágenes diferida
            function initImageOptimization() {
                if (!('IntersectionObserver' in window)) return;
                
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px 0px',
                    threshold: 0.01
                });

                document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                    imageObserver.observe(img);
                });
            }

            // Debounce utility
            if (!window.debounce) {
                window.debounce = (func, wait) => {
                    let timeout;
                    return (...args) => {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                };
            }

            // Función de inicialización de utilidades
            window.initializeGlobalSystems = function() {
                if (typeof requestIdleCallback === 'function') {
                    requestIdleCallback(() => {
                        initImageOptimization();
                    }, { timeout: 2000 });
                } else {
                    setTimeout(() => {
                        initImageOptimization();
                    }, 100);
                }
            };
        }

        // Sistema de alertas global - SOLO PARA USUARIOS AUTENTICADOS
        @auth
        if (!window.globalSystemLoaded) {
            window.globalSystemLoaded = true;

            // Esperar a que Swal esté disponible
            function waitForSwal(callback) {
                if (typeof Swal !== 'undefined') {
                    callback();
                } else {
                    setTimeout(() => waitForSwal(callback), 100);
                }
            }

            waitForSwal(() => {
                class GlobalAlerts {
                    constructor() {
                        this.init();
                    }

                    init() {
                        if (typeof Swal === 'undefined') {
                            console.error('SweetAlert2 no está disponible. Verifica la importación en resources/js/app.js');
                            return;
                        }
                        this.setupEventListeners();
                        this.setupGlobalMethods();
                    }

                    setupEventListeners() {
                        const events = {
                            'swal:success': this.handleSuccess.bind(this),
                            'swal:error': this.handleError.bind(this),
                            'swal:warning': this.handleWarning.bind(this),
                            'swal:info': this.handleInfo.bind(this),
                            'swal:toast': this.handleToast.bind(this)
                        };

                        Object.keys(events).forEach(eventName => {
                            window.addEventListener(eventName, events[eventName]);
                        });
                    }

                    setupGlobalMethods() {
                        window.showSuccess = (title, text = '') => this.success(title, text);
                        window.showError = (title, text = '') => this.error(title, text);
                        window.showToast = (message, type = 'success') => this.toast(message, type);
                        window.confirmDelete = (id, callback) => this.confirmDelete(id, callback);
                    }

                    success(title, text = '') {
                        Swal.fire({
                            icon: 'success',
                            title,
                            text,
                            confirmButtonColor: '#059669',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }

                    error(title, text = '') {
                        Swal.fire({
                            icon: 'error',
                            title,
                            text,
                            confirmButtonColor: '#dc2626'
                        });
                    }

                    toast(message, type = 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: type,
                            title: message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    }

                    confirmDelete(id, callback) {
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: '¡No podrás revertir esta acción!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed && typeof callback === 'function') {
                                callback(id);
                            }
                        });
                    }

                    handleSuccess(event) {
                        this.success(event.detail.title || 'Éxito', event.detail.text || '');
                    }

                    handleError(event) {
                        this.error(event.detail.title || 'Error', event.detail.text || '');
                    }

                    handleWarning(event) {
                        Swal.fire({
                            icon: 'warning',
                            title: event.detail.title || 'Advertencia',
                            text: event.detail.text || '',
                            confirmButtonColor: '#d97706'
                        });
                    }

                    handleInfo(event) {
                        Swal.fire({
                            icon: 'info',
                            title: event.detail.title || 'Información',
                            text: event.detail.text || '',
                            confirmButtonColor: '#2563eb'
                        });
                    }

                    handleToast(event) {
                        const { message, icon } = event.detail;
                        this.toast(message, icon || 'success');
                    }
                }

                // Instanciar GlobalAlerts
                window.GlobalAlerts = GlobalAlerts;
                window.globalAlerts = new GlobalAlerts();
            });
        }
        @endauth

        // Inicialización inicial
        document.addEventListener('DOMContentLoaded', function() {
            window.initializeGlobalSystems();
        });

        // Re-inicialización después de navegación Livewire
        document.addEventListener('livewire:navigated', function() {
            window.initializeGlobalSystems();
        });

        // Compatibilidad adicional
        document.addEventListener('livewire:load', function() {
            window.initializeGlobalSystems();
        });
    </script>
</body>
</html>