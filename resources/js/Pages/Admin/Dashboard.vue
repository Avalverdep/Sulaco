<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../Layouts/AdminLayout.vue'

const secciones = [
  {
    nombre: 'Eventos',
    desc: 'Torneos, partidas y reservas de espacio. Crear, editar y ver inscritos.',
    href: '/admin/eventos',
    activo: true,
  },
  {
    nombre: 'Reservas',
    desc: 'Reservas de producto de los clientes y su estado.',
    href: '/admin/reservas',
    activo: true,
  },
  {
    nombre: 'Inventario',
    desc: 'Productos, stock, categorías y pedidos a proveedor.',
    href: '/admin/productos',
    activo: true,
  },
  {
    nombre: 'Ventas',
    desc: 'Registro de ventas de mostrador y recogidas.',
    href: null,
    activo: false,
  },
  {
    nombre: 'Impuestos',
    desc: 'Resumen de IVA por periodo y exportación.',
    href: null,
    activo: false,
  },
  {
    nombre: 'Usuarios',
    desc: 'Cuentas registradas y gestión de permisos.',
    href: null,
    activo: false,
  },
]
</script>

<template>
  <AdminLayout titulo="Inicio">
    <div class="mb-10">
      <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
        Gestión de la tienda
      </p>
      <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">¿Qué quieres gestionar?</h2>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Secciones disponibles -->
      <Link
        v-for="seccion in secciones.filter(s => s.activo)"
        :key="seccion.nombre"
        :href="seccion.href"
        class="group bg-white border border-black/10 rounded-lg p-6 hover:border-[#E11D2E] transition flex flex-col"
      >
        <div class="h-1 w-10 bg-[#E11D2E] mb-4"></div>
        <h3 class="font-bold text-xl mb-2">{{ seccion.nombre }}</h3>
        <p class="text-sm text-black/55 leading-relaxed flex-1">{{ seccion.desc }}</p>
        <span class="mt-5 text-[13px] font-semibold uppercase tracking-wide text-[#E11D2E] group-hover:translate-x-1 transition-transform inline-block">
          Entrar →
        </span>
      </Link>

      <!-- Secciones aún no disponibles -->
      <div
        v-for="seccion in secciones.filter(s => !s.activo)"
        :key="seccion.nombre"
        class="bg-black/[0.02] border border-dashed border-black/15 rounded-lg p-6 flex flex-col"
      >
        <div class="h-1 w-10 bg-black/15 mb-4"></div>
        <h3 class="font-bold text-xl mb-2 text-black/35">{{ seccion.nombre }}</h3>
        <p class="text-sm text-black/30 leading-relaxed flex-1">{{ seccion.desc }}</p>
        <span class="mt-5 text-[11px] font-semibold uppercase tracking-widest text-black/25">
          Próximamente
        </span>
      </div>
    </div>
  </AdminLayout>
</template>