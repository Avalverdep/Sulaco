<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import logo from '../../img/sulaco-logo.jpeg'
import Aviso from '../Components/Aviso.vue'

defineProps({
  activo: { type: String, default: '' },
})

const usuario = computed(() => usePage().props.auth?.user ?? null)

const menu = [
  { nombre: 'Inicio', href: '/', clave: 'inicio' },
  { nombre: 'Calendario', href: '/eventos', clave: 'eventos' },
  { nombre: 'Catálogo', href: '/catalogo', clave: 'catalogo' },
]
</script>

<template>
  <div
    class="min-h-screen bg-white text-[#16181c] antialiased flex flex-col"
    style="background-image: linear-gradient(rgba(0,0,0,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.06) 1px, transparent 1px); background-size: 48px 48px;"
  >
    <!-- Barra superior -->
    <div class="bg-[#16181c] text-white text-[11px] tracking-[0.14em] uppercase">
      <div class="mx-auto max-w-6xl px-5 py-2 flex flex-wrap items-center gap-x-6 gap-y-1">
        <span>Ruperto Medina 1 · Portugalete</span>
        <span class="hidden sm:inline text-white/40">|</span>
        <span class="hidden sm:inline">622 69 06 42</span>
        <span class="ml-auto text-[#ff5b64]">Reserva y recoge en tienda</span>
      </div>
    </div>

    <!-- Cabecera -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-black/10">
      <div class="mx-auto max-w-6xl px-5 h-20 flex items-center gap-8">
        <Link href="/" class="shrink-0">
          <img :src="logo" alt="Sulaco · Juegos de mesa, wargames y rol" class="h-11 w-auto" />
        </Link>

        <nav class="hidden md:flex items-center gap-1 text-[13px] font-medium tracking-wide uppercase">
          <Link
            v-for="item in menu"
            :key="item.clave"
            :href="item.href"
            class="px-3 py-2 rounded transition"
            :class="activo === item.clave
              ? 'bg-[#E11D2E]/10 text-[#E11D2E]'
              : 'hover:bg-black/5'"
          >
            {{ item.nombre }}
          </Link>
        </nav>

        <div class="ml-auto">
          <a
            v-if="!usuario"
            href="/auth/google"
            class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 border border-black/20 rounded hover:border-black/50 transition"
          >
            Entrar
          </a>

          <Link
            v-else
            :href="usuario.role === 'admin' ? '/admin' : '/user'"
            class="flex items-center gap-2.5 text-[13px] font-semibold tracking-wide uppercase px-3 py-2 border border-black/20 rounded hover:border-black/50 transition"
          >
            <img v-if="usuario.avatar" :src="usuario.avatar" alt="" class="w-7 h-7 rounded-full" />
            <span class="max-w-[8rem] truncate normal-case">{{ usuario.name }}</span>
          </Link>
        </div>
      </div>
    </header>

    <main class="flex-1">
      <slot />
    </main>

    <!-- Pie -->
    <footer class="bg-[#16181c] text-white/70">
      <div class="mx-auto max-w-6xl px-5 py-10 flex flex-wrap items-center justify-between gap-6">
        <div>
          <p class="text-white font-extrabold tracking-[0.2em] text-lg">SULACO</p>
          <p class="text-xs mt-1 tracking-wide">Juegos de mesa, wargames y rol</p>
        </div>
        <p class="text-xs">Ruperto Medina 1 · 48920 Portugalete (Bizkaia)</p>
      </div>
    </footer>

    <Aviso />
  </div>
</template>