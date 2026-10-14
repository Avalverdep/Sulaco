<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import UserLayout from '../../Layouts/UserLayout.vue'
import Confirmacion from '../../Components/Confirmacion.vue'
import Aviso from '../../Components/Aviso.vue'

const props = defineProps({
  reservas: { type: Array, default: () => [] },
  inscripciones: { type: Array, default: () => [] },
})

const usuario = computed(() => usePage().props.auth?.user ?? null)

const filtro = ref('activa')
const aCancelar = ref(null)
const procesando = ref(false)

const filtradas = computed(() =>
  props.reservas.filter((r) => filtro.value === 'todas' || r.estado === filtro.value)
)

const conteos = computed(() => ({
  activa: props.reservas.filter((r) => r.estado === 'activa').length,
  recogida: props.reservas.filter((r) => r.estado === 'recogida').length,
}))

function cancelar() {
  procesando.value = true
  router.delete(`/reservas/${aCancelar.value.id}`, {
    preserveScroll: true,
    onFinish: () => {
      procesando.value = false
      aCancelar.value = null
    },
  })
}

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

    <!-- Reservas -->
    <section class="mb-8">
      <div class="flex flex-wrap items-end justify-between gap-4 mb-4">
        <div>
          <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-1">
            Apartado para ti
          </p>
          <h2 class="text-2xl font-extrabold tracking-tight">Mis reservas</h2>
        </div>

        <div class="flex gap-2">
          <button
            v-for="(etiqueta, valor) in { activa: 'Activas', recogida: 'Recogidas', todas: 'Todas' }"
            :key="valor"
            @click="filtro = valor"
            class="text-[11px] font-semibold uppercase tracking-wide px-3 py-2 rounded border transition"
            :class="filtro === valor
              ? 'bg-[#16181c] text-white border-[#16181c]'
              : 'bg-white border-black/15 text-black/55 hover:border-black/40'"
          >
            {{ etiqueta }}
            <span v-if="conteos[valor]" class="ml-1 opacity-60">{{ conteos[valor] }}</span>
          </button>
        </div>
      </div>

      <div v-if="filtradas.length" class="space-y-3">
        <article
          v-for="reserva in filtradas"
          :key="reserva.id"
          class="bg-white border border-black/10 rounded-lg p-5 flex flex-wrap items-center gap-5"
        >
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-1">
              <h3 class="font-bold">{{ reserva.producto }}</h3>
              <span
                v-if="reserva.pendiente"
                class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded bg-amber-100 text-amber-800"
              >
                Pendiente de llegar
              </span>
            </div>

            <p class="text-sm text-black/55">
              {{ reserva.unidades }} {{ reserva.unidades === 1 ? 'unidad' : 'unidades' }}
              <span v-if="reserva.caduca && reserva.estado === 'activa'">
                · guardada hasta el {{ reserva.caduca }}
              </span>
            </p>

            <p v-if="reserva.posicion" class="text-xs text-black/45 mt-1.5">
              Eres la <strong class="text-[#E11D2E]">{{ reserva.posicion }}ª</strong> reserva
              <span v-if="reserva.unidades_antes > 0">
                · {{ reserva.unidades_antes }}
                {{ reserva.unidades_antes === 1 ? 'unidad apartada' : 'unidades apartadas' }} antes que la tuya
              </span>
              <span v-else>· eres el primero de la cola</span>
            </p>
          </div>

          <button
            v-if="reserva.estado === 'activa'"
            @click="aCancelar = reserva"
            class="text-[12px] font-semibold uppercase tracking-wide px-4 py-2.5 border border-black/20 rounded hover:border-red-400 hover:text-red-600 transition"
          >
            Cancelar
          </button>
          <span v-else class="text-[11px] uppercase tracking-wide text-black/35">Recogida</span>
        </article>
      </div>

      <div v-else class="bg-white border border-dashed border-black/15 rounded-lg p-10 text-center">
        <p class="text-black/50">No tienes reservas {{ filtro === 'todas' ? '' : filtro + 's' }}.</p>
      </div>
    </section>

    <!-- Eventos -->
    <section class="mb-8">
      <div class="mb-4">
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-1">Te esperamos</p>
        <h2 class="text-2xl font-extrabold tracking-tight">Mis eventos</h2>
      </div>

      <div v-if="inscripciones.length" class="space-y-3">
        <article
          v-for="item in inscripciones"
          :key="item.id"
          class="bg-white border border-black/10 rounded-lg p-5 flex items-center gap-5"
        >
          <div class="w-12 shrink-0 text-center">
            <div class="text-2xl font-extrabold leading-none text-[#E11D2E]">{{ item.dia }}</div>
            <div class="text-[10px] uppercase tracking-wider text-black/40 mt-0.5">{{ item.mes }}</div>
          </div>
          <div class="min-w-0">
            <h3 class="font-bold">{{ item.titulo }}</h3>
            <p class="text-sm text-black/50">{{ item.horas }}</p>
          </div>
        </article>
      </div>

      <div v-else class="bg-white border border-dashed border-black/15 rounded-lg p-10 text-center">
        <p class="text-black/50">No estás inscrito en ningún evento.</p>
      </div>
    </section>

    <!-- Gestión de la cuenta -->
    <section class="bg-white border border-black/10 rounded-lg p-6">
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

    <Confirmacion
      :abierto="!!aCancelar"
      titulo="¿Cancelar esta reserva?"
      :texto="aCancelar
        ? `Perderás tu posición en la cola y las unidades volverán al stock. Aitor llorará un poco, pero lo superará.`
        : ''"
      texto-confirmar="Sí, cancelar"
      texto-cancelar="Mejor no"
      peligro
      :procesando="procesando"
      @confirmar="cancelar"
      @cerrar="aCancelar = null"
    />

    <Aviso />
  </UserLayout>
</template>