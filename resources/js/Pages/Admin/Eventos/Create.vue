<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import { Link, usePage} from '@inertiajs/vue3'

const props = defineProps({
  tipos: { type: Array, default: () => [] },
})

const form = useForm({
  kind: 'evento_tienda',
  event_type_id: null,
  title: '',
  description: '',
  fecha: '',
  hora_inicio: '',
  duracion: 2,
  capacity: 8,
})

const esEventoTienda = computed(() => form.kind === 'evento_tienda')


const horaFin = computed(() => {
  if (!form.hora_inicio) return ''
  const [h, m] = form.hora_inicio.split(':').map(Number)
  const fin = new Date(2000, 0, 1, h + Number(form.duracion), m)
  return `${String(fin.getHours()).padStart(2, '0')}:${String(fin.getMinutes()).padStart(2, '0')}`
})

function enviar() {
  form
    .transform((datos) => ({
      kind: datos.kind,
      event_type_id: esEventoTienda.value ? datos.event_type_id : null,
      title: datos.title,
      description: datos.description || null,
      starts_at: `${datos.fecha} ${datos.hora_inicio}:00`,
      ends_at: `${datos.fecha} ${horaFin.value}:00`,
      capacity: esEventoTienda.value ? datos.capacity : null,
    }))
    .post('/admin/eventos')
}
</script>

<template>
  <AdminLayout titulo="Nuevo evento" volver-a="/admin/eventos">
    <div class="max-w-2xl">
      <div class="mb-8">
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
          Programar actividad
        </p>
        <h2 class="text-3xl font-extrabold tracking-tight">Crear evento</h2>
      </div>

      <form @submit.prevent="enviar" class="bg-white border border-black/10 rounded-lg p-6 space-y-6">

        <!-- Tipo de ocupación -->
        <div>
          <label class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Clase de ocupación
          </label>
          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              @click="form.kind = 'evento_tienda'"
              class="px-4 py-3 rounded border text-sm font-semibold transition"
              :class="esEventoTienda
                ? 'border-[#E11D2E] bg-[#E11D2E]/5 text-[#E11D2E]'
                : 'border-black/15 text-black/50 hover:border-black/35'"
            >
              Evento de tienda
            </button>
            <button
              type="button"
              @click="form.kind = 'reserva_usuario'"
              class="px-4 py-3 rounded border text-sm font-semibold transition"
              :class="!esEventoTienda
                ? 'border-[#E11D2E] bg-[#E11D2E]/5 text-[#E11D2E]'
                : 'border-black/15 text-black/50 hover:border-black/35'"
            >
              Mesa reservada
            </button>
          </div>
          <p class="text-xs text-black/40 mt-2">
            {{ esEventoTienda
              ? 'Torneo o partida abierta: la gente puede inscribirse.'
              : 'Ocupación privada: no admite inscripciones.' }}
          </p>
        </div>

        <!-- Título -->
        <div>
          <label for="title" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Título
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            maxlength="120"
            placeholder="Torneo Warhammer 40k"
            class="w-full border border-black/15 rounded px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#E11D2E]"
            :class="form.errors.title ? 'border-red-400' : ''"
          />
          <p v-if="form.errors.title" class="text-xs text-red-600 mt-1.5">{{ form.errors.title }}</p>
        </div>

        <!-- Tipo de evento -->
        <div v-if="esEventoTienda">
          <label for="tipo" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Tipo
          </label>
          <select
            id="tipo"
            v-model="form.event_type_id"
            class="w-full border border-black/15 rounded px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#E11D2E]"
            :class="form.errors.event_type_id ? 'border-red-400' : ''"
          >
            <option :value="null" disabled>Selecciona un tipo…</option>
            <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">{{ tipo.name }}</option>
          </select>
          <p v-if="form.errors.event_type_id" class="text-xs text-red-600 mt-1.5">
            {{ form.errors.event_type_id }}
          </p>
        </div>

        <!-- Fecha y hora -->
        <div class="grid gap-4 sm:grid-cols-3">
          <div>
            <label for="fecha" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
              Día
            </label>
            <input
              id="fecha"
              v-model="form.fecha"
              type="date"
              class="w-full border border-black/15 rounded px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#E11D2E]"
              :class="form.errors.starts_at ? 'border-red-400' : ''"
            />
          </div>

          <div>
            <label for="hora" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
              Empieza
            </label>
            <input
              id="hora"
              v-model="form.hora_inicio"
              type="time"
              step="1800"
              class="w-full border border-black/15 rounded px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#E11D2E]"
            />
          </div>

          <div>
            <label for="duracion" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
              Duración
            </label>
            <select
              id="duracion"
              v-model="form.duracion"
              class="w-full border border-black/15 rounded px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#E11D2E]"
            >
              <option v-for="h in [1, 2, 3, 4, 5, 6, 8]" :key="h" :value="h">{{ h }} h</option>
            </select>
          </div>
        </div>

        <p v-if="form.hora_inicio" class="text-xs text-black/45 -mt-2">
          Horario: {{ form.hora_inicio }} – {{ horaFin }}
        </p>
        <p v-if="form.errors.starts_at" class="text-xs text-red-600 -mt-2">{{ form.errors.starts_at }}</p>
        <p v-if="form.errors.ends_at" class="text-xs text-red-600 -mt-2">{{ form.errors.ends_at }}</p>

        <!-- Aforo -->
        <div v-if="esEventoTienda">
          <label for="capacity" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Plazas
          </label>
          <input
            id="capacity"
            v-model.number="form.capacity"
            type="number"
            min="1"
            max="200"
            class="w-32 border border-black/15 rounded px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#E11D2E]"
            :class="form.errors.capacity ? 'border-red-400' : ''"
          />
          <p v-if="form.errors.capacity" class="text-xs text-red-600 mt-1.5">{{ form.errors.capacity }}</p>
        </div>

        <!-- Descripción -->
        <div>
          <label for="descripcion" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Descripción <span class="normal-case tracking-normal text-black/30">(opcional)</span>
          </label>
          <textarea
            id="descripcion"
            v-model="form.description"
            rows="3"
            maxlength="2000"
            class="w-full border border-black/15 rounded px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#E11D2E]"
          ></textarea>
        </div>

        <!-- Envío -->
        <div class="flex items-center gap-4 pt-2 border-t border-black/10">
          <button
            type="submit"
            :disabled="form.processing"
            class="text-sm font-semibold tracking-wide uppercase px-6 py-3 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ form.processing ? 'Guardando…' : 'Crear evento' }}
          </button>
          <span v-if="form.hasErrors" class="text-xs text-red-600">
            Revisa los campos marcados.
          </span>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>