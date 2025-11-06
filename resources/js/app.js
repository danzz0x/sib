
import "./bootstrap";
import Swal from "sweetalert2";
window.Swal = Swal;

import './global.js'; // Carga utilidades y alertas

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    window.initializeGlobalSystems();
    window.initGlobalAlerts();
});

document.addEventListener('livewire:navigated', () => {
    window.initializeGlobalSystems();
});

document.addEventListener('livewire:load', () => {
    window.initializeGlobalSystems();
});

