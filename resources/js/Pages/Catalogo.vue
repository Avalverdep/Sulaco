<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import PublicLayout from '../Layouts/PublicLayout.vue'

const props = defineProps({
  productos: { type: Array, default: () => [] },
  categorias: { type: Array, default: () => [] },
  filtros: { type: Object, default: () => ({}) },
})

const usuario = computed(() => usePage().props.auth?.user ?? null)
const reservando = ref(null)
const cantidad = ref(1)
const avisoLogin = ref(false)
let temporizadorError = null
const errorReserva = ref(null)

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

function abrirReserva(producto) {
  if (!usuario.value) {
    avisoLogin.value = true
    return
  }
  errorReserva.value = null
  reservando.value = producto
  cantidad.value = 1
}

function confirmarReserva() {
  errorReserva.value = null
  clearTimeout(temporizadorError)
  router.post(`/catalogo/${reservando.value.id}/reservar`,
    { cantidad: cantidad.value },
    {
      preserveScroll: true,
      onSuccess: () => (reservando.value = null),
      onError: (errores) => {
      errorReserva.value = errores.reserva
      temporizadorError = setTimeout(() => (errorReserva.value = null), 5000)}
    }
  )
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
  <PublicLayout activo="catalogo">
    <div class="mx-auto max-w-6xl w-full px-5 py-10">
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
              @click="abrirReserva(producto)"
              :disabled="!producto.reservable"
              class="w-full text-[12px] font-semibold uppercase tracking-wide py-2.5 rounded transition"
              :class="producto.reservable
                ? 'bg-[#E11D2E] text-white hover:bg-[#c4162a]'
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
    </div>  

        <!-- Confirmar reserva -->
    <Teleport to="body">
      <div v-if="reservando" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="reservando = null"></div>
        <div class="relative bg-white border border-black/10 rounded-lg w-full max-w-sm p-6">
          <p class="text-[10px] uppercase tracking-[0.18em] text-[#E11D2E] font-semibold mb-1.5">Reservar</p>
          <h2 class="text-lg font-extrabold leading-tight mb-1">{{ reservando.nombre }}</h2>
          <p class="text-sm text-black/50 mb-5">
            Lo apartamos para ti. Pásate por la tienda a recogerlo.
          </p>

          <label class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Unidades
          </label>
          <div class="flex items-center gap-2 mb-5">
            <button @click="cantidad > 1 && cantidad--" class="w-10 h-10 rounded border border-black/15 font-bold">−</button>
            <span class="flex-1 text-center text-2xl font-extrabold">{{ cantidad }}</span>
            <button @click="cantidad < reservando.maximo && cantidad++" class="w-10 h-10 rounded border border-black/15 font-bold">+</button>
          </div>
          <p v-if="errorReserva" class="text-sm text-red-600 mb-4">{{ errorReserva }}</p>
          <div class="flex gap-2">
            
            <button @click="reservando = null" class="flex-1 text-[13px] font-semibold uppercase tracking-wide py-3 border border-black/20 rounded hover:border-black/50 transition">
              Cancelar
            </button>
            <button @click="confirmarReserva" class="flex-1 text-[13px] font-semibold uppercase tracking-wide py-3 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition">
              Reservar
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Aviso de login -->
    <Teleport to="body">
      <div v-if="avisoLogin" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="avisoLogin = false"></div>
        <div class="relative bg-white border border-black/10 rounded-lg w-full max-w-sm p-6 text-center">
          <h2 class="text-lg font-extrabold mb-2">Necesitas una cuenta</h2>
          <p class="text-sm text-black/55 mb-5">
            Entra con tu cuenta de Google para reservar. Solo guardamos tu nombre y correo.
          </p>
          <div class="flex gap-2">
            <button @click="avisoLogin = false" class="flex-1 text-[13px] font-semibold uppercase tracking-wide py-3 border border-black/20 rounded">
              Ahora no
            </button>
            <a href="/auth/google" class="flex-1 text-[13px] font-semibold uppercase tracking-wide py-3 bg-[#E11D2E] text-white rounded text-center">
              Entrar
            </a>
          </div>
        </div>
      </div>
    </Teleport>
  </PublicLayout>
</template>