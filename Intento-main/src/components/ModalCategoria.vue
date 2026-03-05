<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Categoria } from './Categorias.vue'

interface CategoriaForm {
  nombre: string
  descripcion: string
}

const props = defineProps<{
  show: boolean
  action: 'crear' | 'editar'
  categoria: Categoria | null
  showDelete: boolean
  categoriaAEliminar: Categoria | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'closeDelete'): void
  (e: 'guardar', form: CategoriaForm): void
  (e: 'eliminar'): void
}>()

const form = ref<CategoriaForm>({ nombre: '', descripcion: '' })

watch(() => props.show, (val) => {
  if (val) {
    form.value = props.action === 'editar' && props.categoria
      ? { nombre: props.categoria.nombre, descripcion: props.categoria.descripcion }
      : { nombre: '', descripcion: '' }
  }
})

function guardar(): void {
  if (!form.value.nombre.trim()) return
  emit('guardar', { ...form.value })
}
</script>

<template>
  <Teleport to="body">

    <!-- ── Modal Crear/Editar ── -->
    <Transition name="fade">
      <div v-if="show"
        style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;"
        @click.self="emit('close')">

        <div style="position:absolute;inset:0;background:rgba(122,78,56,0.18);backdrop-filter:blur(4px);"></div>

        <div style="position:relative;background:white;border-radius:24px;padding:36px;width:100%;max-width:460px;box-shadow:0 24px 60px rgba(122,78,56,0.18);font-family:'Jost',sans-serif;">

          <!-- Header -->
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
            <div>
              <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:22px;font-weight:700;color:#7A4E38;margin:0;">
                {{ action === 'crear' ? 'Nueva Categoría' : 'Editar Categoría' }}
              </h3>
              <p style="font-size:12px;color:#A896A0;margin:3px 0 0;">
                {{ action === 'crear' ? 'Completa los datos de la categoría' : 'Modifica los datos de la categoría' }}
              </p>
            </div>
            <button @click="emit('close')"
              style="width:32px;height:32px;border-radius:50%;border:none;background:#F0E8DC;display:flex;align-items:center;justify-content:center;cursor:pointer;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7A4E38" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <!-- Campos -->
          <div style="display:flex;flex-direction:column;gap:16px;">

            <!-- Nombre -->
            <div>
              <label style="font-size:11px;font-weight:600;color:#7A4E38;letter-spacing:0.08em;text-transform:uppercase;display:block;margin-bottom:6px;">Nombre</label>
              <input v-model="form.nombre" type="text" placeholder="Ej. Cabello"
                style="width:100%;padding:11px 14px;border:1.5px solid rgba(212,168,176,0.5);border-radius:12px;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;outline:none;box-sizing:border-box;background:#FDFAF7;"
                @focus="($event.target as HTMLInputElement).style.borderColor='#C4A882'"
                @blur="($event.target as HTMLInputElement).style.borderColor='rgba(212,168,176,0.5)'" />
            </div>

            <!-- Descripción -->
            <div>
              <label style="font-size:11px;font-weight:600;color:#7A4E38;letter-spacing:0.08em;text-transform:uppercase;display:block;margin-bottom:6px;">Descripción</label>
              <textarea v-model="form.descripcion" placeholder="Describe la categoría..." rows="3"
                style="width:100%;padding:11px 14px;border:1.5px solid rgba(212,168,176,0.5);border-radius:12px;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;outline:none;box-sizing:border-box;background:#FDFAF7;resize:vertical;"
                @focus="($event.target as HTMLTextAreaElement).style.borderColor='#C4A882'"
                @blur="($event.target as HTMLTextAreaElement).style.borderColor='rgba(212,168,176,0.5)'"></textarea>
            </div>

          </div>

          <!-- Botones -->
          <div style="display:flex;gap:10px;margin-top:28px;">
            <button @click="emit('close')"
              style="flex:1;padding:12px;border:1.5px solid rgba(212,168,176,0.5);border-radius:50px;background:white;font-size:13px;font-weight:600;color:#A896A0;cursor:pointer;font-family:'Jost',sans-serif;">
              Cancelar
            </button>
            <button @click="guardar"
              style="flex:1;padding:12px;border:none;border-radius:50px;font-size:13px;font-weight:600;color:white;cursor:pointer;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0 0%,#C4A882 50%,#7A4E38 100%);box-shadow:0 6px 18px rgba(122,78,56,0.25);">
              {{ action === 'crear' ? 'Crear Categoría' : 'Guardar Cambios' }}
            </button>
          </div>

        </div>
      </div>
    </Transition>

    <!-- ── Modal Eliminar ── -->
    <Transition name="fade">
      <div v-if="showDelete"
        style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;"
        @click.self="emit('closeDelete')">

        <div style="position:absolute;inset:0;background:rgba(122,78,56,0.18);backdrop-filter:blur(4px);"></div>

        <div style="position:relative;background:white;border-radius:24px;padding:36px;width:100%;max-width:400px;box-shadow:0 24px 60px rgba(122,78,56,0.18);font-family:'Jost',sans-serif;text-align:center;">

          <div style="width:56px;height:56px;border-radius:50%;background:#FFF0F3;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#B05070" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
              <path d="M10 11v6"/><path d="M14 11v6"/>
              <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
          </div>

          <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:22px;font-weight:700;color:#7A4E38;margin:0 0 8px;">Eliminar Categoría</h3>
          <p style="font-size:13px;color:#A896A0;margin:0 0 28px;">
            ¿Estás seguro de eliminar <strong style="color:#7A4E38;">{{ categoriaAEliminar?.nombre }}</strong>? Esta acción no se puede deshacer.
          </p>

          <div style="display:flex;gap:10px;">
            <button @click="emit('closeDelete')"
              style="flex:1;padding:12px;border:1.5px solid rgba(212,168,176,0.5);border-radius:50px;background:white;font-size:13px;font-weight:600;color:#A896A0;cursor:pointer;font-family:'Jost',sans-serif;">
              Cancelar
            </button>
            <button @click="emit('eliminar')"
              style="flex:1;padding:12px;border:none;border-radius:50px;font-size:13px;font-weight:600;color:white;cursor:pointer;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0,#B05070);box-shadow:0 6px 18px rgba(176,80,112,0.3);">
              Sí, eliminar
            </button>
          </div>

        </div>
      </div>
    </Transition>

  </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>