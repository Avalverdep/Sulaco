<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
  tienda: String,
})
</script>

<template>
  <h1>Hola desde Vue</h1>
  <p>Bienvenido a {{ tienda }}</p>
  <Link href="/eventos">Ver eventos</Link>
</template>