<script setup>
import { Link } from '@inertiajs/vue3'
import logo from '../../img/sulaco-logo.jpeg'

defineProps({
  titulo: { type: String, default: '' },
})
</script>

<template>
  <div
    class="min-h-screen bg-white text-[#16181c] antialiased flex flex-col"
    style="background-image: linear-gradient(rgba(0,0,0,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.06) 1px, transparent 1px); background-size: 48px 48px;"
  >
    <div class="bg-[#16181c] text-white text-[11px] tracking-[0.14em] uppercase">
      <div class="mx-auto max-w-5xl px-5 py-2 flex items-center gap-4">
        <span>Mi cuenta</span>
        <Link href="/" class="ml-auto hover:text-white transition">Ir a la web →</Link>
      </div>
    </div>

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-black/10">
      <div class="mx-auto max-w-5xl px-5 h-20 flex items-center gap-6">
        <Link href="/" class="shrink-0">
          <img :src="logo" alt="Sulaco" class="h-10 w-auto" />
        </Link>
        <div v-if="titulo" class="min-w-0 border-l border-black/10 pl-6">
          <p class="text-[10px] uppercase tracking-[0.18em] text-black/40">Área privada</p>
          <h1 class="text-lg font-extrabold tracking-tight truncate">{{ titulo }}</h1>
        </div>
      </div>
    </header>

    <main class="flex-1 mx-auto max-w-5xl w-full px-5 py-12">
      <slot />
    </main>

    <footer class="bg-[#16181c] text-white/60">
      <div class="mx-auto max-w-5xl px-5 py-6 flex flex-wrap items-center justify-between gap-4 text-xs">
        <p class="font-extrabold tracking-[0.2em] text-white text-sm">SULACO</p>
        <p>Ruperto Medina 1 · Portugalete</p>
      </div>
    </footer>
  </div>
</template>