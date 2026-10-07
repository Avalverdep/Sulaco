<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage} from '@inertiajs/vue3'
import logo from '../../img/sulaco-logo.jpeg'
import { computed } from 'vue'
import Aviso from '../Components/Aviso.vue'

const usuario = computed(() => usePage().props.auth.user ?? null)

defineProps({
  eventos: {
    type: Array,
    default: () => [],
  },
})

// PLACEHOLDER PROVISIONAL
const productos = [
  { id: 1, nombre: 'Catan · Edición base', categoria: 'Juego de mesa', precio: '34,95 €' },
  { id: 2, nombre: 'Wingspan', categoria: 'Juego de mesa', precio: '48,00 €' },
  { id: 3, nombre: 'Warhammer 40k · Kill Team', categoria: 'Wargame', precio: '54,90 €' },
  { id: 4, nombre: 'Citadel · Set de pinturas', categoria: 'Modelismo', precio: '29,50 €' },
  { id: 5, nombre: 'Magic · Play Booster', categoria: 'TCG', precio: '4,90 €' },
  { id: 6, nombre: 'La Llamada de Cthulhu 7ª', categoria: 'Rol', precio: '39,95 €' },
  { id: 7, nombre: 'Pokémon · Elite Trainer Box', categoria: 'TCG', precio: '52,95 €' },
  { id: 8, nombre: 'Exploding Kittens', categoria: 'Juego de mesa', precio: '19,95 €' },
]

const horario = [
  { dias: 'Lunes a viernes', horas: '10:00–14:00 · 16:00–20:00' },
  { dias: 'Sábado', horas: '10:00–14:00 · 16:00–20:00' },
  { dias: 'Domingo', horas: 'Cerrado' },
]

// --- Estado del panel de eventos: mini (lateral) o expandido (abajo) ---
const expandido = ref(false)
const seccionEventos = ref(null)
let observer = null
const enFooter = ref(false)
const pie = ref(null)
let observerPie = null

onMounted(() => {
  if (!seccionEventos.value) return

  observer = new IntersectionObserver(
    ([entrada]) => {
      expandido.value = entrada.isIntersecting
    },
    { rootMargin: '-20% 0px -50% 0px' }
  )

  if (pie.value) {
    observerPie = new IntersectionObserver(
      ([entrada]) => {
        console.log('footer visible:', entrada.isIntersecting)
        enFooter.value = entrada.isIntersecting
      }
    )
    observerPie.observe(pie.value)
    console.log(enFooter.value)
  }

  observer.observe(seccionEventos.value)
})

onBeforeUnmount(() => {
  observer?.disconnect()
  observerPie?.disconnect()
})

function scrollLateral(e) {
  const el = e.currentTarget
  if (el.scrollWidth <= el.clientWidth) return
  e.preventDefault()
  el.scrollLeft += e.deltaY
}
</script>

<template>
  <div
  class="min-h-screen bg-white text-[#16181c] antialiased"
  style="background-image: linear-gradient(rgba(0,0,0,0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.06) 1px, transparent 1px); background-size: 48px 48px;"
  >

    <!-- Barra superior -->
    <div class="bg-[#16181c] text-white text-[11px] tracking-[0.14em] uppercase">
      <div class="mx-auto max-w-6xl px-5 py-2 flex flex-wrap items-center gap-x-6 gap-y-1">
        <span>Ruperto Medina 1 · Portugalete</span>
        <span class="hidden sm:inline text-white/40">|</span>
        <span class="hidden sm:inline">622 69 06 42</span>
        <span class="ml-auto text-[#ff5b64]">Mesas disponibles para partidas</span>
      </div>
    </div>

    <!-- Cabecera -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-black/10">
      <div class="mx-auto max-w-6xl px-5 h-20 flex items-center gap-8">
        <Link href="/" class="shrink-0">
          <img :src="logo" alt="Sulaco · Juegos de mesa, wargames y rol" class="h-11 w-auto" />
        </Link>

        <nav class="hidden md:flex items-center gap-1 text-[13px] font-medium tracking-wide uppercase">
          <a href="#novedades" class="px-3 py-2 rounded hover:bg-black/5 transition">Novedades</a>
          <Link href="/catalogo" class="px-3 py-2 rounded hover:bg-black/5 transition">Catálogo</Link>
          <a href="#eventos" class="px-3 py-2 rounded hover:bg-black/5 transition">Eventos</a>
          <a href="#visitanos" class="px-3 py-2 rounded hover:bg-black/5 transition">Visítanos</a>
        </nav>

        <div class="ml-auto">
          <Link
            href="/eventos"
            class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition"
          >
            Calendario
          </Link>

          <a v-if="!usuario" href="/auth/google" class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 border border-black/20 rounded hover:border-black/50 transition" >
            Entrar
          </a>

          <Link v-else :href="usuario.role === 'admin' ? '/admin' : '/user'"
          class="flex items-center gap-2.5 text-[13px] font-semibold tracking-wide uppercase px-3 py-2 border border-black/20 rounded hover:border-black/50 transition"
          >
            <img v-if="usuario.avatar" :src="usuario.avatar" alt="" class="w-7 h-7 rounded-full" />
            <span class="max-w-[8rem] truncate normal-case">{{ usuario.name }}</span>
          </Link>
        </div>
      </div>
    </header>

    <aside
      class="hidden 2xl:block fixed left-8 top-1/2 -translate-y-1/2 z-30 w-52 space-y-3 transition-all duration-700"
      :class="enFooter ? 'opacity-0 -translate-x-16 pointer-events-none' : 'opacity-100 translate-x-0'"
    >
      <div class="bg-white border border-black/10 rounded-lg overflow-hidden">
        <div class="flex items-center gap-2 px-3 py-2.5 border-b border-black/10">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="2" width="20" height="20" rx="5" />
            <circle cx="12" cy="12" r="4" />
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
          </svg>
          <span class="text-[11px] font-bold uppercase tracking-wider">Instagram</span>
        </div>
        <div class="grid grid-cols-2 gap-px bg-black/10">
          <div v-for="n in 4" :key="n" class="aspect-square bg-[#f2f2f3] flex items-center justify-center">
            <span class="text-[9px] uppercase tracking-widest text-black/25">Post</span>
          </div>
        </div>
        <a href="https://www.instagram.com/sulacojuegos_portugalete/" target="_blank" rel="noopener"
          class="block px-3 py-2 text-[10px] font-semibold uppercase tracking-wide text-center border-t border-black/10 hover:bg-black/5 transition">
          Seguir
        </a>
      </div>

      <div class="bg-white border border-black/10 rounded-lg overflow-hidden">
        <div class="flex items-center gap-2 px-3 py-2.5 border-b border-black/10">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-6.4L6.2 22H3l7.3-8.3L2.4 2h6.4l4.4 5.9L18.9 2zm-1.1 18h1.7L8.3 3.8H6.5L17.8 20z" />
          </svg>
          <span class="text-[11px] font-bold uppercase tracking-wider">X</span>
        </div>
        <div class="p-3 space-y-2">
          <div v-for="n in 2" :key="n" class="border border-black/10 rounded p-2">
            <div class="h-1.5 w-3/4 bg-black/10 rounded mb-1.5"></div>
            <div class="h-1.5 w-full bg-black/10 rounded"></div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Hero -->
    <section class="relative overflow-hidden border-b border-black/10">
      <div
        class="pointer-events-none absolute inset-0 opacity-[0.06]"
      ></div>

      <div class="relative mx-auto max-w-6xl px-5 py-20 md:py-24">
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-5">
          Portugalete · Bizkaia
        </p>
        <h1 class="text-4xl md:text-6xl font-extrabold leading-[1.02] tracking-tight max-w-3xl">
          Juegos de mesa, wargames y rol
          <span class="block text-[#E11D2E]">con mesa puesta</span>
        </h1>
        <p class="mt-6 text-lg text-black/60 max-w-xl leading-relaxed">
          Tienda y espacio de juego en el casco viejo. Pásate a probar, reserva tu plaza
          en los torneos o encarga lo que necesites.
        </p>
        <div class="mt-9 flex flex-wrap gap-3">
          <Link
            href="/eventos"
            class="text-sm font-semibold tracking-wide uppercase px-6 py-3.5 bg-[#16181c] text-white rounded hover:bg-black transition"
          >
            Ver próximos eventos
          </Link>
          <a
            href="#visitanos"
            class="text-sm font-semibold tracking-wide uppercase px-6 py-3.5 border border-black/20 rounded hover:border-black/50 transition"
          >
            Cómo llegar
          </a>
        </div>
      </div>
    </section>

    <!-- Novedades: carrusel + panel mini de eventos a la derecha -->
    <section id="novedades" class="mx-auto max-w-6xl px-5 py-16 lg:min-h-[85vh] flex flex-col justify-center">
      <div class="grid gap-8 lg:grid-cols-[1fr_18rem]">

        <!-- Carrusel de productos -->
        <div class="min-w-0">
          <div class="flex flex-wrap items-end justify-between gap-4 mb-6"
          @wheel.prevent="scrollLateral"
          >
            <div>
              <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
                Recién llegado
              </p>
              <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Novedades en tienda</h2>
            </div>
            <p class="text-xs text-black/35 hidden sm:block">Desliza para ver más →</p>
          </div>

          <div class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory">
            <article
              v-for="producto in productos"
              :key="producto.id"
              class="snap-start shrink-0 w-56 border border-black/10 rounded-lg overflow-hidden hover:border-[#E11D2E]/60 transition"
            >
              <div class="h-36 bg-[#f2f2f3] border-b border-black/10 flex items-center justify-center">
                <span class="text-[10px] uppercase tracking-widest text-black/25">Sin imagen</span>
              </div>
              <div class="p-4">
                <p class="text-[10px] uppercase tracking-[0.14em] text-black/40 mb-1.5">
                  {{ producto.categoria }}
                </p>
                <h3 class="font-bold text-sm leading-snug mb-2 min-h-[2.5rem]">{{ producto.nombre }}</h3>
                <p class="font-extrabold text-[#E11D2E]">{{ producto.precio }}</p>
              </div>
            </article>
          </div>
        </div>

        <!-- Panel mini de eventos (se oculta al llegar a la sección completa) -->
        <aside class="hidden lg:block">
          <div
            class="sticky top-28 transition-all duration-500"
            :class="expandido ? 'opacity-0 translate-y-24 scale-95 pointer-events-none' : 'opacity-100 translate-y-0 scale-100'"
          >
            <div class="border border-black/10 rounded-lg overflow-hidden">
              <div class="bg-[#16181c] text-white px-4 py-3">
                <p class="text-[10px] uppercase tracking-[0.18em] text-white/50">Agenda</p>
                <p class="font-bold text-sm mt-0.5">Próximos eventos</p>
              </div>

              <ul v-if="eventos.length" class="divide-y divide-black/10">
                <li v-for="evento in eventos.slice(0, 4)" :key="evento.id" class="flex gap-3 px-4 py-3">
                  <div class="w-9 shrink-0 text-center">
                    <div class="text-lg font-extrabold leading-none text-[#E11D2E]">{{ evento.dia }}</div>
                    <div class="text-[9px] uppercase tracking-wider text-black/40">{{ evento.mes }}</div>
                  </div>
                  <div class="min-w-0">
                    <p class="text-xs font-semibold leading-snug truncate">{{ evento.titulo }}</p>
                    <p class="text-[11px] text-black/45 mt-0.5">{{ evento.hora }}</p>
                  </div>
                </li>
              </ul>
              <p v-else class="px-4 py-6 text-xs text-black/40 text-center">Sin eventos programados.</p>

              <a
                href="#eventos"
                class="block px-4 py-3 text-[11px] font-semibold uppercase tracking-wide text-center border-t border-black/10 hover:bg-black/5 transition"
              >
                Ver todos ↓
              </a>
            </div>
          </div>
        </aside>

      </div>
    </section>

    <!-- Eventos: versión expandida -->
    <section id="eventos" ref="seccionEventos" class="border-y border-black/10 bg-[#f7f7f8]">
      <div
        class="mx-auto max-w-6xl px-5 py-16 md:py-20 transition-all duration-700"
        :class="expandido ? 'opacity-100 translate-y-0' : 'opacity-60 translate-y-4'"
      >
        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
          <div>
            <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
              Actividad en tienda
            </p>
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Próximos eventos</h2>
          </div>
          <Link href="/eventos" class="text-sm font-semibold underline underline-offset-4 hover:text-[#E11D2E] transition">
            Ver calendario completo
          </Link>
        </div>

        <div v-if="eventos.length" class="grid gap-3">
          <article
            v-for="(evento, i) in eventos"
            :key="evento.id"
            :style="{ transitionDelay: expandido ? `${i * 90}ms` : '0ms' }"
            :class="expandido ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            class="group flex flex-wrap items-center gap-5 bg-white border border-black/10 rounded-lg p-4 md:p-5 hover:border-[#E11D2E]/60 transition-all duration-500"
          >
            <div class="w-16 shrink-0 text-center border-r border-black/10 pr-5">
              <div class="text-3xl font-extrabold leading-none text-[#E11D2E]">{{ evento.dia }}</div>
              <div class="text-[11px] uppercase tracking-widest text-black/40 mt-1">{{ evento.mes }}</div>
            </div>

            <div class="flex-1 min-w-[12rem]">
              <p v-if="evento.tipo" class="text-[11px] uppercase tracking-[0.14em] text-black/45 mb-1">
                {{ evento.tipo }}
              </p>
              <h3 class="text-lg font-bold leading-snug">{{ evento.titulo }}</h3>
              <p class="text-sm text-black/55 mt-0.5">
                {{ evento.hora }}<span v-if="evento.plazas"> · {{ evento.plazas }} plazas</span>
              </p>
            </div>

            <Link
              href="/eventos"
              class="text-[13px] font-semibold tracking-wide uppercase px-4 py-2.5 border border-black/15 rounded group-hover:bg-[#E11D2E] group-hover:text-white group-hover:border-[#E11D2E] transition"
            >
              Más info
            </Link>
          </article>
        </div>

        <div v-else class="border border-dashed border-black/15 rounded-lg p-10 text-center bg-white">
          <p class="text-black/50">No hay eventos programados ahora mismo.</p>
          <p class="text-sm text-black/40 mt-1">Vuelve pronto o pregúntanos en tienda.</p>
        </div>
      </div>
    </section>

    <!-- Visítanos -->
    <section id="visitanos" class="mx-auto max-w-6xl px-5 py-16 md:py-20">
      <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
        Dónde estamos
      </p>
      <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-8">Visítanos</h2>

      <div class="grid gap-6 md:grid-cols-2">
        <div class="border border-black/10 rounded-lg p-7">
          <h3 class="font-bold text-lg mb-5">Contacto</h3>
          <dl class="space-y-4 text-sm">
            <div>
              <dt class="text-[11px] uppercase tracking-widest text-black/40 mb-1">Dirección</dt>
              <dd>Calle Ruperto Medina, 1<br />48920 Portugalete, Bizkaia</dd>
            </div>
            <div>
              <dt class="text-[11px] uppercase tracking-widest text-black/40 mb-1">Teléfono</dt>
              <dd>
                <a href="tel:+34622690642" class="text-[#E11D2E] font-medium hover:underline">622 69 06 42</a>
                <span class="text-black/45"> · WhatsApp y Telegram</span>
              </dd>
            </div>
            <div>
              <dt class="text-[11px] uppercase tracking-widest text-black/40 mb-1">Email</dt>
              <dd>
                <a href="mailto:sulacojuegos@hotmail.com" class="text-[#E11D2E] font-medium hover:underline">
                  sulacojuegos@hotmail.com
                </a>
              </dd>
            </div>
          </dl>
        </div>

        <div class="border border-black/10 rounded-lg p-7">
          <h3 class="font-bold text-lg mb-5">Horario</h3>
          <dl class="space-y-3 text-sm">
            <div
              v-for="fila in horario"
              :key="fila.dias"
              class="flex justify-between gap-4 pb-3 border-b border-black/10 last:border-0 last:pb-0"
            >
              <dt class="text-black/55">{{ fila.dias }}</dt>
              <dd class="font-medium text-right">{{ fila.horas }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </section>

    <!-- Pie -->
    <footer ref="pie" class="bg-[#16181c] text-white/70">
      <div class="mx-auto max-w-6xl px-5 py-10 flex flex-wrap items-center justify-between gap-6">
        <div>
          <p class="text-white font-extrabold tracking-[0.2em] text-lg">SULACO</p>
          <p class="text-xs mt-1 tracking-wide">Juegos de mesa, wargames y rol</p>
        </div>
        <p class="text-xs">Ruperto Medina 1 · 48920 Portugalete (Bizkaia)</p>
        <div class="flex items-center gap-3">
          <a href="https://www.instagram.com/sulacojuegos_portugalete/" target="_blank" rel="noopener"
            aria-label="Instagram de Sulaco"
            class="w-10 h-10 flex items-center justify-center border border-white/20 rounded hover:bg-[#E11D2E] hover:border-[#E11D2E] hover:text-white transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="2" width="20" height="20" rx="5" />
              <circle cx="12" cy="12" r="4" />
              <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
            </svg>
          </a>
          <a href="#" aria-label="X de Sulaco"
            class="w-10 h-10 flex items-center justify-center border border-white/20 rounded hover:bg-[#E11D2E] hover:border-[#E11D2E] hover:text-white transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-6.4L6.2 22H3l7.3-8.3L2.4 2h6.4l4.4 5.9L18.9 2z" />
            </svg>
          </a>
        </div>
      </div>
    </footer>
  </div>
  <Aviso />
</template>