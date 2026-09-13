<script setup>
  import { ref, computed, watch } from 'vue'
  import { usePage, router } from '@inertiajs/vue3'
 
  const props = defineProps({
    eventoId: { type: Number, default: null },
  })

  const enviando = ref(false)

  const emit = defineEmits(['cerrar'])

  const evento = ref(null)
  const cargando = ref(false)
  const error = ref(null)

  const usuario = computed(() => usePage().props.auth?.user ?? null)
  const esAdmin = computed(() => usuario.value?.role === 'admin')
  const avisoRegistro = ref(false)

  const confirmacion = ref(null)

  const textoConfirmacion = computed(() => ({
    cancelar: {
      titulo: '¿Cancelar este evento?',
      texto: 'Dejará de aparecer en el calendario y su franja quedará libre. Los inscritos se conservan.',
      boton: 'Sí, cancelar evento',
      clase: 'bg-amber-600 hover:bg-amber-700',
    },
    eliminar: {
      titulo: '¿Eliminar definitivamente?',
      texto: 'El evento se borrará por completo. Esta acción no se puede deshacer.',
      boton: 'Sí, eliminar',
      clase: 'bg-red-600 hover:bg-red-700',
    },
  }[confirmacion.value] ?? null))

  watch(
    () => props.eventoId,
    async (id) => {
      if (!id) {
        evento.value = null
        return
      }

      cargando.value = true
      error.value = null
      evento.value = null

      try {
        const respuesta = await fetch(`/eventos/${id}`, {
          headers: { Accept: 'application/json' },
        })

        if (!respuesta.ok) throw new Error('No se ha podido cargar el evento.')

        evento.value = await respuesta.json()
      } catch (e) {
        error.value = e.message
      } finally {
        cargando.value = false
      }
    },
    { immediate: true }
  )

  function ejecutarConfirmacion() {
    const accion = confirmacion.value
    confirmacion.value = null

    if (accion === 'cancelar') {
      router.patch(`/admin/eventos/${props.eventoId}/cancelar`, {}, {
        onSuccess: () => emit('cerrar'),
      })
    }

    if (accion === 'eliminar') {
      router.delete(`/admin/eventos/${props.eventoId}`, {
        onSuccess: () => emit('cerrar'),
      })
    }
  }

  function cerrar() {
    emit('cerrar')
  }

  function apuntarse() {
    if (!usuario.value) {
      avisoRegistro.value = true
      return
    }

    enviando.value = true
    router.post(`/eventos/${props.eventoId}/inscripcion`, {}, {
      preserveScroll: true,
      onFinish: () => {
        enviando.value = false
        emit('cerrar')
      },
    })
  }

  function desapuntarse() {
    enviando.value = true
    router.delete(`/eventos/${props.eventoId}/inscripcion`, {
      preserveScroll: true,
      onFinish: () => {
        enviando.value = false
        emit('cerrar')
      },
    })
  }

</script>

<template>
  <Teleport to="body">
    <div
      v-if="eventoId"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      @keydown.esc="cerrar"
    >
      <!-- Fondo -->
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cerrar"></div>

      <!-- Ventana -->
      <div class="relative bg-white border border-black/10 rounded-lg w-full max-w-md overflow-hidden shadow-2xl">

        <!-- Cargando -->
        <div v-if="cargando" class="p-10 text-center">
          <p class="text-sm text-black/45">Cargando…</p>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="p-8 text-center">
          <p class="text-sm text-red-600 mb-4">{{ error }}</p>
          <button
            @click="cerrar"
            class="text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 border border-black/20 rounded hover:border-black/50 transition"
          >
            Cerrar
          </button>
        </div>

        <!-- Detalle -->
        <template v-else-if="evento">
          <div class="px-6 py-5 border-b border-black/10">
            <div class="flex items-start gap-4">
              <div class="min-w-0 flex-1">
                <p v-if="evento.tipo" class="text-[10px] uppercase tracking-[0.18em] text-[#E11D2E] font-semibold mb-1.5">
                  {{ evento.tipo }}
                </p>
                <p v-else-if="evento.privado" class="text-[10px] uppercase tracking-[0.18em] text-black/35 font-semibold mb-1.5">
                  Ocupación privada
                </p>
                <h2 class="text-xl font-extrabold tracking-tight leading-tight">{{ evento.titulo }}</h2>
              </div>
              <button
                @click="cerrar"
                aria-label="Cerrar"
                class="shrink-0 w-8 h-8 flex items-center justify-center rounded hover:bg-black/5 text-black/40 text-xl leading-none transition"
              >
                ×
              </button>
            </div>
          </div>

          <div class="px-6 py-5 space-y-4">
            <div class="flex gap-6 text-sm">
              <div>
                <p class="text-[10px] uppercase tracking-widest text-black/40 mb-1">Día</p>
                <p class="font-medium capitalize">{{ evento.fecha }}</p>
              </div>
              <div>
                <p class="text-[10px] uppercase tracking-widest text-black/40 mb-1">Horario</p>
                <p class="font-medium">{{ evento.horas }}</p>
              </div>
            </div>

            <div v-if="evento.descripcion">
              <p class="text-[10px] uppercase tracking-widest text-black/40 mb-1.5">Detalles</p>
              <p class="text-sm text-black/65 leading-relaxed whitespace-pre-line">{{ evento.descripcion }}</p>
            </div>

            <div v-if="evento.privado" class="text-sm text-black/45 bg-black/[0.03] border border-black/10 rounded p-3">
              Esta franja está reservada por un cliente para uso privado.
            </div>

            <!-- Plazas -->
            <div v-if="evento.aforo" class="border border-black/10 rounded p-4">
              <div class="flex items-baseline justify-between mb-2">
                <p class="text-[10px] uppercase tracking-widest text-black/40">Plazas</p>
                <p class="text-sm font-bold" :class="evento.completo ? 'text-black/45' : 'text-[#E11D2E]'">
                  {{ evento.completo ? 'Completo' : `${evento.libres} libres` }}
                </p>
              </div>
              <div class="h-1.5 bg-black/10 rounded overflow-hidden">
                <div
                  class="h-full transition-all"
                  :class="evento.completo ? 'bg-black/25' : 'bg-[#E11D2E]'"
                  :style="{ width: `${((evento.aforo - evento.libres) / evento.aforo) * 100}%` }"
                ></div>
              </div>
            </div>
          </div>

                  <!-- Acciones -->
        <div
          v-if="!evento.privado && !evento.pasado"
          class="px-6 py-4 border-t border-black/10 bg-black/[0.02] space-y-3"
        >
          <!-- Aviso para quien no ha entrado -->
          <div v-if="avisoRegistro" class="bg-white border border-[#E11D2E]/30 rounded p-4">
            <p class="text-sm font-semibold mb-1">Necesitas una cuenta</p>
            <p class="text-xs text-black/55 mb-3">
              Entra con tu cuenta de Google para reservar tu plaza. Solo guardamos tu nombre y correo.
            </p>
            <div class="flex gap-2">
              
                href="/auth/google"
                class="flex-1 text-center text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition"
              <a>
                Entrar con Google
              </a>
              <button
                @click="avisoRegistro = false"
                class="text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 border border-black/20 rounded hover:border-black/50 transition"
              >
                Ahora no
              </button>
            </div>
          </div>

          <!-- Apuntarse (pendiente de lógica) -->
          <button
            v-if="!avisoRegistro"
            @click="evento.inscrito ? desapuntarse() : apuntarse()"
            :disabled="enviando || (evento.completo && !evento.inscrito)"
            class="w-full text-sm font-semibold uppercase tracking-wide px-4 py-3 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
            :class="evento.inscrito
              ? 'border border-black/25 hover:border-black/50'
              : 'bg-[#E11D2E] text-white hover:bg-[#c4162a]'"
            >
            {{ enviando ? 'Un momento…'
              : evento.inscrito ? 'Desapuntarme'
              : evento.completo ? 'Avisarme si queda hueco'
              : 'Apuntarme' }}
          </button>

          <!-- Acciones de administrador -->
          <div v-if="esAdmin" class="pt-3 border-t border-black/10 flex gap-2">
            <button
              @click="confirmacion = 'cancelar'"
              class="flex-1 text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 border border-amber-400 text-amber-700 rounded hover:bg-amber-50 transition"
            >
              Cancelar evento
            </button>
            <button
              @click="confirmacion = 'eliminar'"
              class="flex-1 text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 border border-red-400 text-red-700 rounded hover:bg-red-50 transition"
            >
              Eliminar
            </button>
          </div>
        </div>

        <div
          v-else-if="evento.pasado"
          class="px-6 py-4 border-t border-black/10 bg-black/[0.02]"
        >
          <p class="text-sm text-black/40 text-center">Este evento ya ha terminado.</p>
        </div>
          <div v-else-if="evento.pasado" class="px-6 py-4 border-t border-black/10 bg-black/[0.02]">
            <p class="text-sm text-black/40 text-center">Este evento ya ha terminado.</p>
          </div>
        </template>
      </div>

      <!-- Confirmación -->
        <div v-if="textoConfirmacion" class="absolute inset-0 z-10 flex items-center justify-center p-5 bg-white/95 backdrop-blur-sm">
        <div class="text-center">
            <p class="font-extrabold text-lg mb-2">{{ textoConfirmacion.titulo }}</p>
            <p class="text-sm text-black/55 mb-6 max-w-xs mx-auto leading-relaxed">
            {{ textoConfirmacion.texto }}
            </p>
            <div class="flex gap-2 justify-center">
            <button
                @click="confirmacion = null"
                class="text-[13px] font-semibold uppercase tracking-wide px-5 py-2.5 border border-black/20 rounded hover:border-black/50 transition"
            >
                No, volver
            </button>
            <button
                @click="ejecutarConfirmacion"
                class="text-[13px] font-semibold uppercase tracking-wide px-5 py-2.5 text-white rounded transition"
                :class="textoConfirmacion.clase"
            >
                {{ textoConfirmacion.boton }}
            </button>
            </div>
        </div>
        </div>
    </div>
  </Teleport>
</template>