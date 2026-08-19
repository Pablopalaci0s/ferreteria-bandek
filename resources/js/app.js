import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('carrito', {
        items: JSON.parse(localStorage.getItem('bandek-carrito') || '[]'),
        abierto: false,

        guardar() {
            localStorage.setItem('bandek-carrito', JSON.stringify(this.items));
        },

        agregar(producto, cantidad = 1) {
            const existente = this.items.find((i) => i.id === producto.id);

            if (existente) {
                existente.cantidad += cantidad;
            } else {
                this.items.push({ ...producto, cantidad });
            }

            this.guardar();
            this.abierto = true;
        },

        quitar(id) {
            this.items = this.items.filter((i) => i.id !== id);
            this.guardar();
        },

        actualizarCantidad(id, cantidad) {
            const item = this.items.find((i) => i.id === id);

            if (! item) return;

            item.cantidad = Math.max(1, cantidad);
            this.guardar();
        },

        vaciar() {
            this.items = [];
            this.guardar();
        },

        get cantidadTotal() {
            return this.items.reduce((suma, i) => suma + i.cantidad, 0);
        },

        get total() {
            return this.items.reduce((suma, i) => suma + (i.precio * i.cantidad), 0);
        },
    });
});

Alpine.start();
