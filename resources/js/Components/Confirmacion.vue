<script setup>
defineProps({
  abierto: { type: Boolean, default: false },
  titulo: { type: String, required: true },
  texto: { type: String, default: '' },
  textoConfirmar: { type: String, default: 'Confirmar' },
  textoCancelar: { type: String, default: 'Volver' },
  peligro: { type: Boolean, default: false },
  procesando: { type: Boolean, default: false },
})

const emit = defineEmits(['confirmar', 'cerrar'])
</script>

<template>
  <Teleport to="body">
    <div v-if="abierto" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="emit('cerrar')"></div>

      <div class="relative bg-white border border-black/10 rounded-lg w-full max-w-sm p-6 text-center shadow-2xl">
        <h2 class="text-lg font-extrabold tracking-tight mb-2">{{ titulo }}</h2>
        <p v-if="texto" class="text-sm text-black/55 mb-6 leading-relaxed">{{ texto }}</p>

        <div class="flex gap-2">
          <button
            @click="emit('cerrar')"
            :disabled="procesando"
            class="flex-1 text-[13px] font-semibold uppercase tracking-wide py-3 border border-black/20 rounded hover:border-black/50 transition disabled:opacity-50"
          >
            {{ textoCancelar }}
          </button>
          <button
            @click="emit('confirmar')"
            :disabled="procesando"
            class="flex-1 text-[13px] font-semibold uppercase tracking-wide py-3 text-white rounded transition disabled:opacity-50"
            :class="peligro ? 'bg-red-600 hover:bg-red-700' : 'bg-[#E11D2E] hover:bg-[#c4162a]'"
          >
            {{ procesando ? 'Un momento…' : textoConfirmar }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>