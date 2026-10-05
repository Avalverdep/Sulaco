<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

defineProps({
  reservas: { type: Array, default: () => [] },
  estado: { type: String, default: 'activa' },
  conteos: { type: Object, default: () => ({}) },
})

const filtros = { activa: 'Activas', recogida: 'Recogidas', cancelada: 'Canceladas', caducada: 'Caducadas', todas: 'Todas' }

function filtrar(valor) {
  router.get('/admin/reservas', { estado: valor }, { preserveState: true })
}

function recoger(id) {
  if (!confirm('¿Marcar como recogida? Se registrará como entregada al cliente.')) return
  router.patch(`/admin/reservas/${id}/recoger`, {}, { preserveScroll: true })
}

function cancelar(id) {
  if (!confirm('¿Cancelar esta reserva? El stock volverá al inventario.')) return
  router.patch(`/admin/reservas/${id}/cancelar`, {}, { preserveScroll: true })
}
</script>

<template>
  <AdminLayout titulo="Reservas" volver-a="/admin">
    <div class="mb-6">
      <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">Pedidos de clientes</p>
      <h2 class="text-3xl font-extrabold tracking-tight">Reservas</h2>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
      <button
        v-for="(etiqueta, valor) in filtros"
        :key="valor"
        @click="filtrar(valor)"
        class="text-[12px] font-semibold uppercase tracking-wide px-4 py-2.5 rounded border transition"
        :class="estado === valor ? 'bg-[#16181c] text-white border-[#16181c]' : 'bg-white border-black/15 text-black/60 hover:border-black/40'"
      >
        {{ etiqueta }}
        <span v-if="conteos[valor]" class="ml-1 opacity-60">{{ conteos[valor] }}</span>
      </button>
    </div>

    <div v-if="reservas.length" class="bg-white border border-black/10 rounded-lg overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-black/10 text-left">
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Producto</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Cliente</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Ud.</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Fechas</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in reservas" :key="r.id" class="border-b border-black/10 last:border-0 hover:bg-black/[0.02]">
            <td class="px-5 py-4 font-semibold">{{ r.producto }}</td>
            <td class="px-5 py-4">
              <p>{{ r.cliente }}</p>
              <p class="text-[11px] text-black/40">{{ r.email }}</p>
            </td>
            <td class="px-5 py-4 font-bold">{{ r.unidades }}</td>
            <td class="px-5 py-4 text-black/55 text-xs">
              <p>Creada: {{ r.creada }}</p>
              <p :class="r.vencida ? 'text-red-600 font-semibold' : ''">
                Caduca: {{ r.caduca ?? '—' }}{{ r.vencida ? ' · vencida' : '' }}
              </p>
            </td>
            <td class="px-5 py-4">
              <div v-if="r.estado === 'activa'" class="flex gap-2 justify-end">
                <button @click="recoger(r.id)" class="text-[11px] font-semibold uppercase tracking-wide px-3 py-2 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition">
                  Recogida
                </button>
                <button @click="cancelar(r.id)" class="text-[11px] font-semibold uppercase tracking-wide px-3 py-2 border border-black/20 rounded hover:border-red-400 hover:text-red-600 transition">
                  Cancelar
                </button>
              </div>
              <span v-else class="text-[11px] uppercase tracking-wide text-black/35">{{ r.estado }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="bg-white border border-dashed border-black/15 rounded-lg p-12 text-center">
      <p class="text-black/50">No hay reservas con ese filtro.</p>
    </div>
  </AdminLayout>
</template>