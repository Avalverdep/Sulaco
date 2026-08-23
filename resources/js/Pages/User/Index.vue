<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import UserLayout from '../../Layouts/UserLayout.vue'

const usuario = computed(() => usePage().props.auth?.user ?? null)

// PENDIENTE: llegarán del controlador cuando existan las consultas
defineProps({
  inscripciones: { type: Array, default: () => [] },
  reservas: { type: Array, default: () => [] },
})

function cerrarSesion() {
  router.post('/logout')
}
</script>

<template>
  <UserLayout titulo="Mi cuenta">

    <!-- Perfil -->
    <section class="bg-white border border-black/10 rounded-lg p-6 mb-6 flex flex-wrap items-center gap-5">
      <img
        v-if="usuario?.avatar"
        :src="usuario.avatar"
        alt=""
        class="w-16 h-16 rounded-full border border-black/10"
      />
      <div
        v-else
        class="w-16 h-16 rounded-full bg-black/5 border border-black/10 flex items-center justify-center text-black/30 text-xl font-bold"
      >
        {{ usuario?.name?.charAt(0) ?? '?' }}
      </div>

      <div class="min-w-0">
        <p class="text-[10px] uppercase tracking-[0.18em] text-black/40 mb-0.5">Conectado como</p>
        <p class="text-xl font-extrabold tracking-tight truncate">{{ usuario?.name }}</p>
      </div>

      <button
        @click="cerrarSesion"
        class="ml-auto text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 border border-black/20 rounded hover:border-black/50 transition"
      >
        Cerrar sesión
      </button>
    </section>

    <div class="grid gap-6 md:grid-cols-2">

      <!-- Inscripciones a eventos -->
      <section class="bg-white border border-black/10 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-black/10 flex items-center justify-between">
          <div>
            <div class="h-1 w-8 bg-[#E11D2E] mb-2"></div>
            <h2 class="font-bold text-lg">Mis eventos</h2>
          </div>
          <span class="text-xs text-black/35">{{ inscripciones.length }}</span>
        </div>

        <ul v-if="inscripciones.length" class="divide-y divide-black/10">
          <li v-for="item in inscripciones" :key="item.id" class="px-5 py-4 flex items-center gap-4">
            <div class="w-11 shrink-0 text-center">
              <div class="text-xl font-extrabold leading-none text-[#E11D2E]">{{ item.dia }}</div>
              <div class="text-[9px] uppercase tracking-wider text-black/40 mt-0.5">{{ item.mes }}</div>
            </div>
            <div class="min-w-0 flex-1">
              <p class="font-semibold text-sm truncate">{{ item.titulo }}</p>
              <p class="text-xs text-black/45 mt-0.5">{{ item.horas }}</p>
            </div>
          </li>
        </ul>

        <p v-else class="px-5 py-10 text-center text-sm text-black/40">
          No estás inscrito en ningún evento.
        </p>
      </section>

      <!-- Reservas de producto -->
      <section class="bg-white border border-black/10 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-black/10 flex items-center justify-between">
          <div>
            <div class="h-1 w-8 bg-[#E11D2E] mb-2"></div>
            <h2 class="font-bold text-lg">Mis reservas</h2>
          </div>
          <span class="text-xs text-black/35">{{ reservas.length }}</span>
        </div>

        <ul v-if="reservas.length" class="divide-y divide-black/10">
          <li v-for="item in reservas" :key="item.id" class="px-5 py-4">
            <p class="font-semibold text-sm">{{ item.producto }}</p>
            <p class="text-xs text-black/45 mt-0.5">{{ item.unidades }} ud. · {{ item.estado }}</p>
          </li>
        </ul>

        <p v-else class="px-5 py-10 text-center text-sm text-black/40">
          No tienes reservas activas.
        </p>
      </section>
    </div>

    <!-- Gestión de la cuenta -->
    <section class="mt-6 bg-white border border-black/10 rounded-lg p-6">
      <div class="h-1 w-8 bg-black/20 mb-3"></div>
      <h2 class="font-bold text-lg mb-1">Gestión de la cuenta</h2>
      <p class="text-sm text-black/50 mb-5 max-w-2xl">
        Guardamos tu nombre, correo e imagen de perfil de Google únicamente para gestionar tus
        inscripciones y reservas. No almacenamos contraseñas.
      </p>

      <div class="flex flex-wrap gap-3">
        <span class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 bg-black/5 text-black/30 rounded cursor-not-allowed">
          Descargar mis datos
        </span>
        <span class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 bg-black/5 text-black/30 rounded cursor-not-allowed">
          Borrar mi cuenta
        </span>
      </div>
      <p class="text-[11px] text-black/30 mt-3">Estas opciones estarán disponibles próximamente.</p>
    </section>

  </UserLayout>
</template>