<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import ProductoForm from '../../../Components/ProductoForm.vue'

const props = defineProps({
  producto: { type: Object, required: true },
  categorias: { type: Array, default: () => [] },
})

const form = useForm({
  name: props.producto.name,
  category_id: props.producto.category_id,
  subcategoria_id: null,
  price: props.producto.price,
  status: props.producto.status,
  description: props.producto.description ?? '',
  max_per_user: props.producto.max_per_user,
  reservation_days: props.producto.reservation_days,
  imagen: null,
})

const confirmarBorrado = ref(false)

function enviar() {
  form.post(`/admin/productos/${props.producto.id}`, { forceFormData: true })
}

function eliminar() {
  router.delete(`/admin/productos/${props.producto.id}`)
}
</script>

<template>
  <AdminLayout titulo="Editar producto" volver-a="/admin/productos">
    <div class="max-w-4xl">
      <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
          <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">Inventario</p>
          <h2 class="text-3xl font-extrabold tracking-tight">{{ producto.name }}</h2>
        </div>
        <p class="text-sm text-black/45">
          Stock actual: <span class="font-bold text-black">{{ producto.stock }}</span> ud.
          <span class="block text-[11px] text-black/35">Se ajusta desde el listado de inventario.</span>
        </p>
      </div>

      <div class="bg-white border border-black/10 rounded-lg p-6">
        <ProductoForm
          :form="form"
          :categorias="categorias"
          :imagen-actual="producto.imagen"
          texto-boton="Guardar cambios"
          @enviar="enviar"
        />
      </div>

      <!-- Zona de riesgo -->
      <div class="mt-8 border border-red-200 bg-red-50/40 rounded-lg p-6">
        <h3 class="font-bold text-sm uppercase tracking-wide text-red-700 mb-2">Eliminar producto</h3>
        <p class="text-sm text-black/55 mb-4 max-w-2xl">
          Solo se puede eliminar si no tiene reservas ni ventas asociadas. Si las tiene, márcalo como
          <strong>descatalogado</strong> arriba: desaparecerá de la web y conservarás el historial.
        </p>

        <button
          v-if="!confirmarBorrado"
          @click="confirmarBorrado = true"
          class="text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 border border-red-400 text-red-700 rounded hover:bg-red-50 transition"
        >
          Eliminar producto
        </button>

        <div v-else class="flex flex-wrap gap-2 items-center">
          <span class="text-sm font-semibold text-red-700">¿Seguro? No se puede deshacer.</span>
          <button
            @click="eliminar"
            class="text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 bg-red-600 text-white rounded hover:bg-red-700 transition"
          >
            Sí, eliminar
          </button>
          <button
            @click="confirmarBorrado = false"
            class="text-[13px] font-semibold uppercase tracking-wide px-4 py-2.5 border border-black/20 rounded"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>