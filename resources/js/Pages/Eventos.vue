<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import EventoModal from '../Components/EventoModal.vue'
import PublicLayout from '../Layouts/PublicLayout.vue'

const props = defineProps({
  eventos: { type: Array, default: () => [] },
  dias: { type: Array, default: () => [] },
  semanaActual: { type: String, required: true },
  semanaAnterior: { type: String, required: true },
  semanaSiguiente: { type: String, required: true },
  rotulo: { type: String, default: '' },
})

const eventoAbierto = ref(null)

// Rejilla: de 10:00 a 20:00, bloques de una hora
const HORA_INICIO = 10
const HORA_FIN = 20
const ALTO_HORA = 72 // píxeles por hora

const horas = computed(() => {
  const lista = []
  for (let h = HORA_INICIO; h < HORA_FIN; h++) lista.push(h)
  return lista
})

const altoRejilla = computed(() => (HORA_FIN - HORA_INICIO) * ALTO_HORA)

// Traduce un evento a su posición y altura dentro de la columna del día
function estiloBloque(evento) {
  const inicio = Math.max(evento.horaInicio, HORA_INICIO)
  const fin = Math.min(evento.horaFin, HORA_FIN)
  const top = (inicio - HORA_INICIO) * ALTO_HORA
  const alto = Math.max((fin - inicio) * ALTO_HORA, 34)

  return { top: `${top}px`, height: `${alto - 4}px` }
}

function eventosDelDia(fecha) {
  return props.eventos.filter((e) => e.fecha === fecha)
}

function irASemana(semana) {
  router.get('/eventos', { semana }, { preserveScroll: true })
}
</script>

<template>
  <PublicLayout activo="eventos">
   

    <div class="mx-auto max-w-6xl w-full px-5 py-10">
      <div class="mb-8">
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
          Actividad en tienda
        </p>
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Calendario de eventos</h1>
        <p class="mt-3 text-black/55 max-w-xl">
          Consulta los torneos y partidas programados. Para apuntarte necesitas entrar con tu cuenta.
        </p>
      </div>

      <!-- Navegación entre semanas -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <button
          @click="irASemana(semanaAnterior)"
          class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 bg-white border border-black/20 rounded hover:border-black/50 transition"
        >
          ← Semana anterior
        </button>

        <p class="font-bold tracking-tight text-lg">{{ rotulo }}</p>

        <button
          @click="irASemana(semanaSiguiente)"
          class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 bg-white border border-black/20 rounded hover:border-black/50 transition"
        >
          Semana siguiente →
        </button>
      </div>

      <!-- Rejilla del calendario -->
      <div class="bg-white border border-black/10 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <div class="min-w-[52rem]">

            <!-- Cabecera de días -->
            <div class="grid grid-cols-[4rem_repeat(7,1fr)] border-b border-black/10">
              <div class="border-r border-black/10"></div>
              <div
                v-for="dia in dias"
                :key="dia.fecha"
                class="px-2 py-3 text-center border-r border-black/10 last:border-r-0"
                :class="dia.esHoy ? 'bg-[#E11D2E]/5' : ''"
              >
                <p class="text-[10px] uppercase tracking-widest text-black/40">{{ dia.nombre }}</p>
                <p
                  class="text-xl font-extrabold mt-0.5"
                  :class="dia.esHoy ? 'text-[#E11D2E]' : ''"
                >{{ dia.numero }}</p>
              </div>
            </div>

            <!-- Cuerpo: horas + columnas de días -->
            <div class="grid grid-cols-[4rem_repeat(7,1fr)] relative" :style="{ height: `${altoRejilla}px` }">

              <!-- Columna de horas -->
              <div class="border-r border-black/10">
                <div
                  v-for="hora in horas"
                  :key="hora"
                  class="border-b border-black/8 last:border-b-0 px-2 text-right"
                  :style="{ height: `${ALTO_HORA}px` }"
                >
                  <span class="text-[11px] font-medium text-black/35 relative -top-1.5">
                    {{ String(hora).padStart(2, '0') }}:00
                  </span>
                </div>
              </div>

              <!-- Una columna por día -->
              <div
                v-for="dia in dias"
                :key="dia.fecha"
                class="relative border-r border-black/10 last:border-r-0"
                :class="dia.esHoy ? 'bg-[#E11D2E]/[0.03]' : ''"
              >
                <!-- Líneas de fondo por hora -->
                <div
                  v-for="hora in horas"
                  :key="hora"
                  class="border-b border-black/8 last:border-b-0"
                  :style="{ height: `${ALTO_HORA}px` }"
                ></div>

                <!-- Bloques de evento, posicionados encima -->
                <article
                  @click="eventoAbierto = evento.id"
                  v-for="evento in eventosDelDia(dia.fecha)"
                  :key="evento.id"
                  class="absolute left-1 right-1 rounded border px-2 py-1.5 overflow-hidden transition hover:shadow-md cursor-pointer"
                  :class="evento.completo
                    ? 'bg-black/5 border-black/15 text-black/45'
                    : 'bg-[#E11D2E]/10 border-[#E11D2E]/40 hover:bg-[#E11D2E]/15'"
                  :style="estiloBloque(evento)"
                  :title="`${evento.titulo} · ${evento.horas}`"
                >
                  <p class="text-[10px] font-semibold uppercase tracking-wider truncate"
                     :class="evento.completo ? 'text-black/40' : 'text-[#E11D2E]'">
                    {{ evento.horas }}
                  </p>
                  <p class="text-xs font-bold leading-tight line-clamp-2">{{ evento.titulo }}</p>
                  <p v-if="evento.aforo" class="text-[10px] mt-0.5"
                     :class="evento.completo ? 'text-black/40' : 'text-black/50'">
                    {{ evento.completo ? 'Completo' : `${evento.libres} plazas libres` }}
                  </p>
                </article>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Leyenda -->
      <div class="mt-5 flex flex-wrap items-center gap-5 text-xs text-black/45">
        <span class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-sm bg-[#E11D2E]/10 border border-[#E11D2E]/40"></span>
          Plazas disponibles
        </span>
        <span class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-sm bg-black/5 border border-black/15"></span>
          Completo
        </span>
        <span class="ml-auto">Horario de la rejilla: 10:00 – 20:00</span>
      </div>

      <p v-if="!eventos.length" class="mt-6 text-center text-black/45">
        No hay nada programado esta semana.
      </p>
    

    
    
    
  </div>
  <EventoModal :evento-id="eventoAbierto" @cerrar="eventoAbierto = null" />
</PublicLayout>
</template>