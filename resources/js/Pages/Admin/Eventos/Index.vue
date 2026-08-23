<script setup>
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import { Link, usePage} from '@inertiajs/vue3'

defineProps({
  eventos: { type: Array, default: () => [] },
})

const colorEstado = {
  aprobado: 'bg-green-50 text-green-700 border-green-200',
  pendiente: 'bg-amber-50 text-amber-700 border-amber-200',
  rechazado: 'bg-red-50 text-red-700 border-red-200',
  cancelado: 'bg-black/5 text-black/45 border-black/15',
}
</script>

<template>
  <AdminLayout titulo="Eventos" volver-a="/admin">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
      <div>
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
          Torneos, partidas y reservas
        </p>
        <h2 class="text-3xl font-extrabold tracking-tight">Todos los eventos</h2>
      </div>

      
        <Link href="/admin/eventos/crear" class="text-[13px] font-semibold tracking-wide uppercase px-5 py-3 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition">
          + Nuevo evento
        </Link>
      
    </div>

    <div v-if="eventos.length" class="bg-white border border-black/10 rounded-lg overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-black/10 text-left">
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Evento</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Tipo</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Fecha</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Aforo</th>
            <th class="px-5 py-3 text-[10px] uppercase tracking-widest text-black/40 font-semibold">Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="evento in eventos"
            :key="evento.id"
            class="border-b border-black/8 last:border-0 hover:bg-black/[0.02] transition"
          >
            <td class="px-5 py-4">
              <p class="font-semibold">{{ evento.titulo }}</p>
              <p class="text-[11px] uppercase tracking-wider text-black/35 mt-0.5">{{ evento.clase }}</p>
            </td>
            <td class="px-5 py-4 text-black/55">{{ evento.tipo ?? '—' }}</td>
            <td class="px-5 py-4 text-black/55 whitespace-nowrap">
              {{ evento.fecha }}<br />
              <span class="text-black/35">{{ evento.horas }}</span>
            </td>
            <td class="px-5 py-4 text-black/55">
              <span v-if="evento.aforo">{{ evento.inscritos }} / {{ evento.aforo }}</span>
              <span v-else class="text-black/30">—</span>
            </td>
            <td class="px-5 py-4">
              <span
                class="text-[11px] font-semibold uppercase tracking-wide px-2.5 py-1 rounded border"
                :class="colorEstado[evento.estado]"
              >
                {{ evento.estado }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="bg-white border border-dashed border-black/15 rounded-lg p-12 text-center">
      <p class="text-black/50">Todavía no hay eventos creados.</p>
      <p class="text-sm text-black/35 mt-1">Cuando crees el primero, aparecerá aquí.</p>
    </div>
  </AdminLayout>
</template>