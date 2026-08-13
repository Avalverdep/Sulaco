<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  proximos: Array,
})
</script>

<template>
  <h1>Próximos eventos</h1>
  <ul>
    <li v-for="evento in proximos" :key="evento">{{ evento }}</li>
  </ul>
  <Link href="/">Volver al inicio</Link>
</template>