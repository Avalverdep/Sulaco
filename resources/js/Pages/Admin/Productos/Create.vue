<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import ProductoForm from '../../../Components/ProductoForm.vue'

defineProps({
  categorias: { type: Array, default: () => [] },
})

const form = useForm({
  name: '',
  category_id: null,
  price: '',
  stock: 0,
  status: 'disponible',
  description: '',
  max_per_user: 2,
  imagen: null,
})

function enviar() {
  form.post('/admin/productos', { forceFormData: true })
}
</script>

<template>
  <AdminLayout titulo="Nuevo producto" volver-a="/admin/productos">
    <div class="max-w-4xl">
      <div class="mb-8">
        <p class="text-[11px] font-semibold tracking-[0.24em] uppercase text-[#E11D2E] mb-2">Inventario</p>
        <h2 class="text-3xl font-extrabold tracking-tight">Añadir producto</h2>
      </div>

      <div class="bg-white border border-black/10 rounded-lg p-6">
        <ProductoForm
          :form="form"
          :categorias="categorias"
          texto-boton="Añadir al inventario"
          @enviar="enviar"
        />
      </div>
    </div>
  </AdminLayout>
</template>