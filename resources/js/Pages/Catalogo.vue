<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import logo from '@/../img/sulaco-logo.jpeg'
import Aviso from '../Components/Aviso.vue'

const props = defineProps({
  productos: { type: Array, default: () => [] },
  categorias: { type: Array, default: () => [] },
  filtros: { type: Object, default: () => ({}) },
})

const busqueda = ref(props.filtros.buscar ?? '')
let temporizador = null


const categoriaActiva = computed(() => {
  const slug = props.filtros.categoria
  if (!slug) return null

  return (
    props.categorias.find((c) => c.slug === slug) ??
    props.categorias.find((c) => c.hijas?.some((h) => h.slug === slug)) ??
    null
  )
})

function filtrarPor(slug) {
  router.get('/catalogo', { categoria: slug, buscar: busqueda.value || undefined }, {
    preserveState: true,
    preserveScroll: true,
  })
}

watch(busqueda, (texto) => {
  clearTimeout(temporizador)
  temporizador = setTimeout(() => {
    router.get('/catalogo', {
      categoria: props.filtros.categoria || undefined,
      buscar: texto || undefined,
    }, { preserveState: true, preserveScroll: true, replace: true })
  }, 350)
})
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
          <img :src="logo" alt="Sulaco" class="h-11 w-auto" />
        </Link>
        <nav class="hidden md:flex items-center gap-1 text-[13px] font-medium tracking-wide uppercase">
          <Link href="/" class="px-3 py-2 rounded hover:bg-black/5 transition">Inicio</Link>
          <Link href="/eventos" class="px-3 py-2 rounded hover:bg-black/5 transition">Calendario</Link>
          <span class="px-3 py-2 rounded bg-[#E11D2E]/10 text-[#E11D2E]">Catálogo</span>
        </nav>
        <a
          href="/auth/google"
          class="ml-auto text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 border border-black/20 rounded hover:border-black/50 transition"
        >
          Entrar
        </a>
      </div>
    </header>

    <main class="flex-1 mx-auto max-w-6xl w-full px-5 py-10">
      <div class="mb-8">
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
          Lo que tenemos en tienda
        </p>
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Catálogo</h1>
        <p class="mt-3 text-black/55 max-w-xl">
          Reserva lo que te interese y pásate a recogerlo por la tienda. No hacemos envíos.
        </p>
      </div>

      <!-- Categorías principales -->
      <div class="flex flex-wrap gap-2 mb-4">
        <button
          @click="filtrarPor(undefined)"
          class="text-[12px] font-semibold uppercase tracking-wide px-4 py-2.5 rounded border transition"
          :class="!filtros.categoria
            ? 'bg-[#16181c] text-white border-[#16181c]'
            : 'bg-white border-black/15 text-black/60 hover:border-black/40'"
        >
          Todo
        </button>
        <button
          v-for="categoria in categorias"
          :key="categoria.id"
          @click="filtrarPor(categoria.slug)"
          class="text-[12px] font-semibold uppercase tracking-wide px-4 py-2.5 rounded border transition"
          :class="categoriaActiva?.id === categoria.id
            ? 'bg-[#16181c] text-white border-[#16181c]'
            : 'bg-white border-black/15 text-black/60 hover:border-black/40'"
        >
          {{ categoria.name }}
        </button>
      </div>

      <!-- Subcategorías de la principal activa -->
      <div v-if="categoriaActiva?.hijas?.length" class="flex flex-wrap gap-2 mb-6 pl-1">
        <button
          @click="filtrarPor(categoriaActiva.slug)"
          class="text-[11px] font-medium px-3 py-1.5 rounded-full border transition"
          :class="filtros.categoria === categoriaActiva.slug
            ? 'bg-[#E11D2E]/10 border-[#E11D2E] text-[#E11D2E]'
            : 'bg-white border-black/15 text-black/50 hover:border-black/35'"
        >
          Todo {{ categoriaActiva.name }}
        </button>
        <button
          v-for="hija in categoriaActiva.hijas"
          :key="hija.id"
          @click="filtrarPor(hija.slug)"
          class="text-[11px] font-medium px-3 py-1.5 rounded-full border transition"
          :class="filtros.categoria === hija.slug
            ? 'bg-[#E11D2E]/10 border-[#E11D2E] text-[#E11D2E]'
            : 'bg-white border-black/15 text-black/50 hover:border-black/35'"
        >
          {{ hija.name }}
        </button>
      </div>

      <!-- Buscador -->
      <div class="mb-8">
        <input
          v-model="busqueda"
          type="search"
          placeholder="Buscar por nombre…"
          class="w-full sm:max-w-sm border border-black/15 rounded px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
        />
      </div>

      <!-- Rejilla de productos -->
      <div v-if="productos.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <article
          v-for="producto in productos"
          :key="producto.id"
          class="bg-white border border-black/10 rounded-lg overflow-hidden flex flex-col transition hover:border-[#E11D2E]/60"
        >
          <div class="aspect-square bg-[#f2f2f3] border-b border-black/10 relative">
            <img v-if="producto.imagen" :src="producto.imagen" alt="" class="w-full h-full object-cover" />
            <div v-else class="absolute inset-0 flex items-center justify-center">
              <span class="text-[10px] uppercase tracking-widest text-black/25">Sin foto</span>
            </div>

            <span
              v-if="producto.proximamente"
              class="absolute top-2 left-2 text-[10px] font-semibold uppercase tracking-wider px-2 py-1 rounded bg-amber-500 text-white"
            >
              Próximamente
            </span>
          </div>

          <div class="p-4 flex-1 flex flex-col">
            <p class="text-[10px] uppercase tracking-[0.14em] text-black/40 mb-1">{{ producto.categoria }}</p>
            <h2 class="font-bold text-sm leading-snug mb-2 flex-1">{{ producto.nombre }}</h2>
            <p class="font-extrabold text-lg text-[#E11D2E] mb-3">{{ producto.precio }}</p>

            <button
              disabled
              class="w-full text-[12px] font-semibold uppercase tracking-wide py-2.5 rounded transition"
              :class="producto.reservable
                ? 'bg-black/10 text-black/35 cursor-not-allowed'
                : 'border border-black/10 text-black/30 cursor-not-allowed'"
            >
              {{ producto.reservable ? 'Reservar' : 'Agotado' }}
            </button>
          </div>
        </article>
      </div>

      <div v-else class="bg-white border border-dashed border-black/15 rounded-lg p-12 text-center">
        <p class="text-black/50">No hemos encontrado nada con esos filtros.</p>
        <p class="text-sm text-black/35 mt-1">Prueba con otra categoría o pregúntanos en tienda.</p>
      </div>

      <p class="mt-8 text-xs text-black/35 text-center">
        Las reservas estarán disponibles próximamente.
      </p>
    </main>

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