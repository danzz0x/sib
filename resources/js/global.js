// Evitar redeclaración
window.globalSystemLoaded = window.globalSystemLoaded || false;
window.utilsLoaded = window.utilsLoaded || false;

// ---------------------------
// UTILIDADES GLOBALES
// ---------------------------
if (!window.utilsLoaded) {
    window.utilsLoaded = true;

    // Lazy load de imágenes
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

        document.querySelectorAll('img.lazy').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Debounce global
    if (!window.debounce) {
        window.debounce = (func, wait) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        };
    }

    // Inicialización general
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

// ---------------------------
// ALERTAS GLOBALES (USUARIOS AUTENTICADOS)
// ---------------------------
function initGlobalAlerts() {
    // Solo para usuarios autenticados
     if (!window.isAuthenticated) return;

    if (!window.globalSystemLoaded) {
        window.globalSystemLoaded = true;

        const SwalGlobal = window.Swal;

        class GlobalAlerts {
            constructor() { this.init(SwalGlobal); }

            init(Swal) {
                this.Swal = Swal;
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
                window.showSuccess = (title, text='') => this.success(title, text);
                window.showError = (title, text='') => this.error(title, text);
                window.showToast = (message, type='success') => this.toast(message, type);
                window.confirmDelete = (id, callback) => this.confirmDelete(id, callback);
                window.confirmStatus = (message, id, callback) => this.confirmStatus(message, id, callback);
            }

            success(title, text='') { this.Swal.fire({ icon:'success', title, text, confirmButtonColor:'#059669', timer:3000, timerProgressBar:true, showConfirmButton:false }); }
            error(title, text='') { this.Swal.fire({ icon:'error', title, text, confirmButtonColor:'#dc2626' }); }
            toast(message, type='success') { this.Swal.fire({ toast:true, position:'top-end', icon:type, title:message, showConfirmButton:false, timer:3000, timerProgressBar:true, didOpen:(toast)=>{ toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); } }); }
            confirmDelete(id, callback) { this.Swal.fire({ title:'¿Estás seguro?', text:'¡No podrás revertir esta acción!', icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280', confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar', reverseButtons:true }).then(result=>{ if(result.isConfirmed && typeof callback==='function'){ callback(id); } }); }

            confirmStatus(message, id, callback) { this.Swal.fire({ title:message, icon:'info', showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280', confirmButtonText:'Sí', cancelButtonText:'Cancelar', reverseButtons:true }).then(result=>{ if(result.isConfirmed && typeof callback==='function'){ callback(id); } }); }

            handleSuccess(event){ this.success(event.detail.title||'Éxito', event.detail.text||''); }
            handleError(event){ this.error(event.detail.title||'Error', event.detail.text||''); }
            handleWarning(event){ this.Swal.fire({ icon:'warning', title:event.detail.title||'Advertencia', text:event.detail.text||'', confirmButtonColor:'#d97706' }); }
            handleInfo(event){ this.Swal.fire({ icon:'info', title:event.detail.title||'Información', text:event.detail.text||'', confirmButtonColor:'#2563eb' }); }
            handleToast(event){ const { message, icon } = event.detail; this.toast(message, icon||'success'); }
        }

        window.GlobalAlerts = GlobalAlerts;
        window.globalAlerts = new GlobalAlerts();
    }
}

// Ejecutar alertas después de cargar JS
window.initGlobalAlerts = initGlobalAlerts;
