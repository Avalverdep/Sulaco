<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps({
  productos: { type: Array, default: () => [] },
  categorias: { type: Array, default: () => [] },
})

const busqueda = ref('')
const filtroCategoria = ref('todas')
const filtroEstado = ref('activos')
const fijando = ref(null) // id del producto con el diálogo "=" abierto
const cantidadFija = ref('')

const filtrados = computed(() => {
  const texto = busqueda.value.trim().toLowerCase()

  return props.productos.filter((p) => {
    if (texto && !p.nombre.toLowerCase().includes(texto)) return false
    if (filtroCategoria.value !== 'todas' && p.categoria_id !== filtroCategoria.value) return false
    if (filtroEstado.value === 'activos' && p.estado === 'descatalogado') return false
    if (filtroEstado.value !== 'activos' && filtroEstado.value !== 'todos' && p.estado !== filtroEstado.value) return false
    return true
  })
})

const etiquetaEstado = {
  pedido: 'Encargado',
  disponible: 'En tienda',
  descatalogado: 'Descatalogado',
}

function claseStock(stock) {
  if (stock === 0) return 'text-red-600'
  if (stock <= 3) return 'text-amber-600'
  return 'text-black'
}

function ajustar(producto, delta) {
  if (producto.stock + delta < 0) return
  router.patch(`/admin/productos/${producto.id}/stock`, { delta }, { preserveScroll: true })
}

function abrirFijar(producto) {
  fijando.value = producto.id
  cantidadFija.value = producto.stock
}

function confirmarFijar(producto) {
  const cantidad = Number(cantidadFija.value)
  if (!Number.isInteger(cantidad) || cantidad < 0) return

  router.patch(`/admin/productos/${producto.id}/stock`, { cantidad }, {
    preserveScroll: true,
    onFinish: () => (fijando.value = null),
  })
}
</script>

<template>
  <AdminLayout titulo="Inventario" volver-a="/admin">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
      <div>
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">
          Productos y stock
        </p>
        <h2 class="text-3xl font-extrabold tracking-tight">Inventario</h2>
      </div>

      <Link
        href="/admin/productos/crear"
        class="text-[13px] font-semibold tracking-wide uppercase px-5 py-3 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition"
      >
        + Nuevo producto
      </Link>
    </div>

    <!-- Herramientas -->
    <div class="bg-white border border-black/10 rounded-lg p-4 mb-6 flex flex-wrap items-center gap-3">
      <input
        v-model="busqueda"
        type="search"
        placeholder="Buscar producto…"
        class="flex-1 min-w-[12rem] border border-black/15 rounded px-3.5 py-2 text-sm focus:outline-none focus:border-[#E11D2E]"
      />

      <select
        v-model="filtroCategoria"
        class="border border-black/15 rounded px-3 py-2 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
      >
        <option value="todas">Todas las categorías</option>
        <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>

      <div class="flex gap-1">
        <button
          v-for="(etiqueta, valor) in { activos: 'Activos', pedido: 'Encargados', disponible: 'En tienda', todos: 'Todos' }"
          :key="valor"
          @click="filtroEstado = valor"
          class="text-[11px] font-semibold uppercase tracking-wide px-3 py-2 rounded border transition"
          :class="filtroEstado === valor
            ? 'bg-[#16181c] text-white border-[#16181c]'
            : 'border-black/15 text-black/55 hover:border-black/35'"
        >
          {{ etiqueta }}
        </button>
      </div>
    </div>

    <!-- Rejilla -->
    <div v-if="filtrados.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <article
        v-for="producto in filtrados"
        :key="producto.id"
        class="bg-white border border-black/10 rounded-lg overflow-hidden flex flex-col transition hover:border-black/30"
        :class="producto.estado === 'descatalogado' ? 'opacity-50' : ''"
      >
        <!-- Foto -->
        <div class="aspect-square bg-[#f2f2f3] border-b border-black/10 relative">
          <img v-if="producto.miniatura" :src="producto.miniatura" alt="" class="w-full h-full object-cover" />
          <div v-else class="absolute inset-0 flex items-center justify-center">
            <span class="text-[10px] uppercase tracking-widest text-black/25">Sin foto</span>
          </div>

          <span
            class="absolute top-2 left-2 text-[10px] font-semibold uppercase tracking-wider px-2 py-1 rounded bg-white/95 border border-black/10"
            :class="producto.estado === 'pedido' ? 'text-amber-700' : producto.estado === 'disponible' ? 'text-green-700' : 'text-black/40'"
          >
            {{ etiquetaEstado[producto.estado] }}
          </span>
        </div>

        <!-- Datos -->
        <div class="p-4 flex-1 flex flex-col">
          <p class="text-[10px] uppercase tracking-[0.14em] text-black/40 mb-1">{{ producto.categoria }}</p>
          <h3 class="font-bold text-sm leading-snug mb-2 flex-1">{{ producto.nombre }}</h3>
          <p class="font-extrabold text-lg text-[#E11D2E] mb-3">{{ producto.precio }}</p>

          <!-- Stock -->
          <div class="border-t border-black/10 pt-3">
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] uppercase tracking-widest text-black/40">Unidades</span>
              <span class="text-[10px] font-semibold uppercase tracking-wider" :class="claseStock(producto.stock)">
                {{ producto.stock === 0 ? 'Agotado' : producto.stock <= 3 ? '¡Quedan pocas!' : '' }}
              </span>
            </div>

            <div v-if="fijando !== producto.id" class="flex items-center gap-1">
              <button
                @click="ajustar(producto, -1)"
                :disabled="producto.stock === 0"
                class="w-9 h-9 rounded border border-black/15 font-bold hover:border-black/40 transition disabled:opacity-30"
                aria-label="Quitar una unidad"
              >−</button>

              <span class="flex-1 text-center text-xl font-extrabold" :class="claseStock(producto.stock)">
                {{ producto.stock }}
              </span>

              <button
                @click="ajustar(producto, 1)"
                class="w-9 h-9 rounded border border-black/15 font-bold hover:border-black/40 transition"
                aria-label="Añadir una unidad"
              >+</button>

              <button
                @click="abrirFijar(producto)"
                class="w-9 h-9 rounded border border-black/15 text-xs font-bold hover:border-black/40 transition"
                aria-label="Fijar cantidad exacta"
                title="Fijar cantidad"
              >=</button>
            </div>

            <div v-else class="flex items-center gap-1">
              <input
                v-model="cantidadFija"
                type="number"
                min="0"
                class="flex-1 border border-[#E11D2E] rounded px-2 py-1.5 text-center text-sm focus:outline-none"
                @keydown.enter="confirmarFijar(producto)"
                @keydown.esc="fijando = null"
                autofocus
              />
              <button
                @click="confirmarFijar(producto)"
                class="h-9 px-3 rounded bg-[#E11D2E] text-white text-xs font-bold"
              >OK</button>
              <button
                @click="fijando = null"
                class="h-9 px-2 rounded border border-black/15 text-xs"
              >×</button>
            </div>
          </div>

          <Link
            :href="`/admin/productos/${producto.id}/editar`"
            class="mt-3 block text-center text-[12px] font-semibold uppercase tracking-wide py-2 border border-black/15 rounded hover:border-[#E11D2E] hover:text-[#E11D2E] transition"
          >
            Editar
          </Link>
        </div>
      </article>
    </div>

    <div v-else class="bg-white border border-dashed border-black/15 rounded-lg p-12 text-center">
      <p class="text-black/50">
        {{ productos.length ? 'Ningún producto coincide con el filtro.' : 'Todavía no hay productos.' }}
      </p>
      <p v-if="!productos.length" class="text-sm text-black/35 mt-1">Añade el primero con el botón de arriba.</p>
    </div>
  </AdminLayout>
</template>