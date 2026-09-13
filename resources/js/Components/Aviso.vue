<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const visible = ref(false)
const mensaje = ref('')
let temporizador = null

watch(
  () => usePage().props.flash?.exito,
  (texto) => {
    if (!texto) return

    mensaje.value = texto
    visible.value = true

    clearTimeout(temporizador)
    temporizador = setTimeout(() => (visible.value = false), 4000)
  },
  { immediate: true }
)
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-y-4"
      leave-active-class="transition duration-200 ease-in"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="visible"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] bg-[#16181c] text-white rounded-lg shadow-xl px-5 py-3.5 flex items-center gap-3 max-w-md"
        role="status"
      >
        <span class="w-6 h-6 shrink-0 rounded-full bg-[#E11D2E] flex items-center justify-center text-sm">✓</span>
        <p class="text-sm">{{ mensaje }}</p>
        <button
          @click="visible = false"
          aria-label="Cerrar aviso"
          class="ml-2 text-white/40 hover:text-white transition text-lg leading-none"
        >
          ×
        </button>
      </div>
    </Transition>
  </Teleport>
</template>