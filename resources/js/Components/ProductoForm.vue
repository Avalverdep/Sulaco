<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  form: { type: Object, required: true },
  categorias: { type: Array, default: () => [] },
  imagenActual: { type: String, default: null },
  textoBoton: { type: String, default: 'Guardar' },
})

const emit = defineEmits(['enviar'])

const masOpciones = ref(false)
const arrastrando = ref(false)
const previsualizacion = ref(null)
const inputArchivo = ref(null)

const estados = [
  { valor: 'pedido', nombre: 'Encargado', desc: 'Pedido al proveedor. Los clientes ya pueden reservarlo.' },
  { valor: 'disponible', nombre: 'En tienda', desc: 'Disponible para reservar y comprar.' },
  { valor: 'descatalogado', nombre: 'Descatalogado', desc: 'No se muestra en la web.' },
]

const imagenMostrada = computed(() => previsualizacion.value ?? props.imagenActual)

function seleccionarArchivo(archivo) {
  if (!archivo || !archivo.type.startsWith('image/')) return
  props.form.imagen = archivo
  previsualizacion.value = URL.createObjectURL(archivo)
}

function alSoltar(e) {
  arrastrando.value = false
  seleccionarArchivo(e.dataTransfer.files[0])
}

function alElegir(e) {
  seleccionarArchivo(e.target.files[0])
}

function quitarImagen() {
  props.form.imagen = null
  previsualizacion.value = null
  if (inputArchivo.value) inputArchivo.value.value = ''
}
</script>

<template>
  <form @submit.prevent="emit('enviar')" class="grid gap-8 lg:grid-cols-[18rem_1fr]">

    <!-- Imagen -->
    <div>
      <label class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
        Foto
      </label>

      <div
        class="relative aspect-square rounded-lg border-2 border-dashed transition overflow-hidden cursor-pointer"
        :class="arrastrando ? 'border-[#E11D2E] bg-[#E11D2E]/5' : 'border-black/15 hover:border-black/35 bg-white'"
        @dragover.prevent="arrastrando = true"
        @dragleave.prevent="arrastrando = false"
        @drop.prevent="alSoltar"
        @click="inputArchivo.click()"
      >
        <img v-if="imagenMostrada" :src="imagenMostrada" alt="" class="w-full h-full object-cover" />

        <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
          <p class="text-sm font-semibold text-black/60">Arrastra una foto aquí</p>
          <p class="text-xs text-black/40 mt-1">o pulsa para elegirla</p>
          <p class="text-[10px] text-black/30 mt-3">JPG, PNG o WebP · máx. 5 MB</p>
        </div>

        <input
          ref="inputArchivo"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          class="hidden"
          @change="alElegir"
        />
      </div>

      <button
        v-if="imagenMostrada"
        type="button"
        @click="quitarImagen"
        class="mt-2 text-xs text-black/45 hover:text-[#E11D2E] transition"
      >
        Quitar foto
      </button>

      <p v-if="form.errors.imagen" class="text-xs text-red-600 mt-1.5">{{ form.errors.imagen }}</p>
      <p class="text-[11px] text-black/35 mt-2">Se recortará automáticamente a formato cuadrado.</p>
    </div>

    <!-- Campos -->
    <div class="space-y-6">

      <div>
        <label for="nombre" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
          Nombre
        </label>
        <input
          id="nombre"
          v-model="form.name"
          type="text"
          maxlength="120"
          placeholder="Catan · Edición base"
          class="w-full border rounded px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
          :class="form.errors.name ? 'border-red-400' : 'border-black/15'"
        />
        <p v-if="form.errors.name" class="text-xs text-red-600 mt-1.5">{{ form.errors.name }}</p>
      </div>

      <div class="grid gap-4 sm:grid-cols-3">
        <div>
          <label for="categoria" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Categoría
          </label>
          <select
            id="categoria"
            v-model="form.category_id"
            class="w-full border rounded px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
            :class="form.errors.category_id ? 'border-red-400' : 'border-black/15'"
          >
            <option :value="null" disabled>Elige una…</option>
            <option v-for="c in categorias" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
          <p v-if="form.errors.category_id" class="text-xs text-red-600 mt-1.5">{{ form.errors.category_id }}</p>
        </div>

        <div>
          <label for="precio" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Precio
          </label>
          <div class="relative">
            <input
              id="precio"
              v-model="form.price"
              type="number"
              step="0.01"
              min="0"
              placeholder="0,00"
              class="w-full border rounded pl-3.5 pr-9 py-2.5 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
              :class="form.errors.price ? 'border-red-400' : 'border-black/15'"
            />
            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm text-black/40">€</span>
          </div>
          <p v-if="form.errors.price" class="text-xs text-red-600 mt-1.5">{{ form.errors.price }}</p>
        </div>

        <div>
          <label for="stock" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
            Unidades
          </label>
          <input
            id="stock"
            v-model.number="form.stock"
            type="number"
            min="0"
            class="w-full border rounded px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
            :class="form.errors.stock ? 'border-red-400' : 'border-black/15'"
          />
          <p v-if="form.errors.stock" class="text-xs text-red-600 mt-1.5">{{ form.errors.stock }}</p>
        </div>
      </div>

      <!-- Estado -->
      <div>
        <label class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
          Estado
        </label>
        <div class="grid gap-3 sm:grid-cols-3">
          <button
            v-for="estado in estados"
            :key="estado.valor"
            type="button"
            @click="form.status = estado.valor"
            class="text-left p-4 rounded border transition"
            :class="form.status === estado.valor
              ? 'border-[#E11D2E] bg-[#E11D2E]/5'
              : 'border-black/15 bg-white hover:border-black/35'"
          >
            <p class="font-semibold text-sm" :class="form.status === estado.valor ? 'text-[#E11D2E]' : ''">
              {{ estado.nombre }}
            </p>
            <p class="text-xs text-black/50 mt-1 leading-snug">{{ estado.desc }}</p>
          </button>
        </div>
        <p v-if="form.errors.status" class="text-xs text-red-600 mt-1.5">{{ form.errors.status }}</p>
      </div>

      <!-- Más opciones -->
      <div class="border-t border-black/10 pt-4">
        <button
          type="button"
          @click="masOpciones = !masOpciones"
          class="text-xs font-semibold uppercase tracking-wide text-black/50 hover:text-black transition"
        >
          {{ masOpciones ? '− Menos opciones' : '+ Más opciones' }}
        </button>

        <div v-if="masOpciones" class="mt-5 space-y-5">
          <div>
            <label for="descripcion" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
              Descripción
            </label>
            <textarea
              id="descripcion"
              v-model="form.description"
              rows="3"
              maxlength="2000"
              class="w-full border border-black/15 rounded px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
            ></textarea>
          </div>

          <div>
            <label for="maximo" class="block text-[11px] uppercase tracking-widest text-black/45 font-semibold mb-2">
              Límite por cliente
            </label>
            <input
              id="maximo"
              v-model.number="form.max_per_user"
              type="number"
              min="1"
              max="50"
              class="w-28 border border-black/15 rounded px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:border-[#E11D2E]"
            />
            <p class="text-xs text-black/40 mt-1.5">
              Cuántas unidades puede reservar una misma persona. Útil para novedades con pocas existencias.
            </p>
          </div>
        </div>
      </div>

      <!-- Envío -->
      <div class="flex items-center gap-4 pt-2 border-t border-black/10">
        <button
          type="submit"
          :disabled="form.processing"
          class="text-sm font-semibold tracking-wide uppercase px-6 py-3 bg-[#E11D2E] text-white rounded hover:bg-[#c4162a] transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ form.processing ? 'Guardando…' : textoBoton }}
        </button>
        <span v-if="form.hasErrors" class="text-xs text-red-600">Revisa los campos marcados.</span>
      </div>
    </div>
  </form>
</template>