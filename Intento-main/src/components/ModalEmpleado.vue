<script setup lang="ts">
import { ref, watch } from 'vue'

// ── Types ──────────────────────────────────────────────────────
export interface Empleado {
  id: number
  nombre: string
  correo: string
  telefono: string
  cargo: string
  estado: 'activo' | 'inactivo'
  fechaContratacion: string
}

interface EmpleadoForm {
  nombre: string
  correo: string
  telefono: string
  cargo: string
  estado: 'activo' | 'inactivo'
  fechaContratacion: string
  password: string
}

// ── Props & Emits ──────────────────────────────────────────────
const props = defineProps<{
  show: boolean
  action: 'crear' | 'editar'
  empleado?: Empleado | null
  showDelete: boolean
  empleadoAEliminar?: Empleado | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'closeDelete'): void
  (e: 'guardar', form: EmpleadoForm): void
  (e: 'eliminar'): void
}>()

// ── State ──────────────────────────────────────────────────────
const showPwd   = ref<boolean>(false)
const formError = ref<string>('')

function emptyForm(): EmpleadoForm {
  return { nombre: '', correo: '', telefono: '', cargo: '', estado: 'activo', fechaContratacion: '', password: '' }
}
const form = ref<EmpleadoForm>(emptyForm())

watch(() => props.empleado, (e) => {
  if (props.action === 'editar' && e) {
    form.value = {
      nombre: e.nombre,
      correo: e.correo,
      telefono: e.telefono,
      cargo: e.cargo,
      estado: e.estado,
      fechaContratacion: e.fechaContratacion,
      password: ''
    }
  } else {
    form.value = emptyForm()
  }
})

watch(() => props.show, (val) => {
  if (val) {
    formError.value = ''
    showPwd.value = false
    if (props.action === 'crear') form.value = emptyForm()
  }
})

// ── Methods ────────────────────────────────────────────────────
function guardar(): void {
  if (!form.value.nombre.trim())               { formError.value = 'El nombre es obligatorio.'; return }
  if (!form.value.correo.trim())               { formError.value = 'El correo es obligatorio.'; return }
  if (!form.value.telefono.trim())             { formError.value = 'El teléfono es obligatorio.'; return }
  if (form.value.telefono.length !== 10)       { formError.value = 'El teléfono debe tener 10 dígitos.'; return }
  if (!form.value.cargo.trim())                { formError.value = 'El cargo es obligatorio.'; return }
  if (!form.value.fechaContratacion)           { formError.value = 'La fecha de contratación es obligatoria.'; return }
  if (props.action === 'crear' && !form.value.password) { formError.value = 'La contraseña es obligatoria.'; return }
  if (form.value.password && form.value.password.length < 6) { formError.value = 'La contraseña debe tener mínimo 6 caracteres.'; return }
  emit('guardar', { ...form.value })
}
</script>

<template>
  <!-- ── Modal Crear / Editar ── -->
  <Transition name="nk-modal">
    <div v-if="show"
      style="position:fixed;inset:0;background:rgba(90,55,35,0.4);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:200;padding:20px;font-family:'Jost',sans-serif;"
      @click.self="emit('close')">
      <div style="background:white;border-radius:24px;width:100%;max-width:480px;box-shadow:0 24px 64px rgba(122,78,56,0.25);overflow:hidden;">

        <!-- Header -->
        <div style="padding:24px 24px 16px;display:flex;align-items:flex-start;justify-content:space-between;">
          <div style="flex:1;">
            <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:22px;font-weight:700;color:#7A4E38;margin:0;text-align:left;">
              {{ action === 'crear' ? 'Nuevo Empleado' : 'Editar Empleado' }}
            </h3>
            <p style="font-size:12px;color:#A896A0;margin:4px 0 0;text-align:left;">
              {{ action === 'crear' ? 'Completa los datos del empleado' : 'Actualizar información' }}
            </p>
          </div>
          <button @click="emit('close')" style="border:none;background:rgba(240,232,220,0.8);border-radius:50%;width:32px;height:32px;cursor:pointer;color:#A896A0;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-left:12px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>

        <!-- Body -->
        <div style="padding:24px 24px 16px;max-height:65vh;overflow-y:auto;">
          <div v-if="formError" style="margin-bottom:14px;padding:10px 14px;border-radius:12px;background:rgba(212,120,120,0.1);border:1px solid rgba(212,120,120,0.25);color:#7A4E38;font-size:12px;text-align:center;">
            {{ formError }}
          </div>

          <!-- Nombre -->
          <div style="margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:6px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Nombre Completo *
            </label>
            <input v-model="form.nombre" type="text" placeholder="Ej: Sofía González Ruiz"
              style="width:100%;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);outline:none;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;box-sizing:border-box;"
              @focus="($event.target as HTMLInputElement).style.cssText+='border-color:#D4A8B0;background:white;box-shadow:0 0 0 3px rgba(212,168,176,0.15)'"
              @blur="($event.target as HTMLInputElement).style.cssText+='border-color:rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);box-shadow:none'"
              @input="formError=''"/>
          </div>

          <!-- Correo -->
          <div style="margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:6px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              Correo Electrónico *
            </label>
            <input v-model="form.correo" type="email" placeholder="ejemplo@correo.com"
              style="width:100%;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);outline:none;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;box-sizing:border-box;"
              @focus="($event.target as HTMLInputElement).style.cssText+='border-color:#D4A8B0;background:white;box-shadow:0 0 0 3px rgba(212,168,176,0.15)'"
              @blur="($event.target as HTMLInputElement).style.cssText+='border-color:rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);box-shadow:none'"
              @input="formError=''"/>
          </div>

          <!-- Teléfono -->
          <div style="margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:6px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.86a16 16 0 0 0 6.29 6.29l1.29-1.29a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              Teléfono *
            </label>
            <input v-model="form.telefono" type="tel" placeholder="8711234567" maxlength="10"
              style="width:100%;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);outline:none;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;box-sizing:border-box;"
              @focus="($event.target as HTMLInputElement).style.cssText+='border-color:#D4A8B0;background:white;box-shadow:0 0 0 3px rgba(212,168,176,0.15)'"
              @blur="($event.target as HTMLInputElement).style.cssText+='border-color:rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);box-shadow:none'"
              @input="formError=''"/>
            <p style="font-size:10px;color:#A896A0;margin:4px 0 0 4px;">10 dígitos</p>
          </div>

          <!-- Cargo -->
          <div style="margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:6px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
              Cargo *
            </label>
            <input v-model="form.cargo" type="text" placeholder="Ej: Estilista, Manicurista..."
              style="width:100%;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);outline:none;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;box-sizing:border-box;"
              @focus="($event.target as HTMLInputElement).style.cssText+='border-color:#D4A8B0;background:white;box-shadow:0 0 0 3px rgba(212,168,176,0.15)'"
              @blur="($event.target as HTMLInputElement).style.cssText+='border-color:rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);box-shadow:none'"
              @input="formError=''"/>
          </div>

          <!-- Fecha Contratación -->
          <div style="margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:6px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              Fecha de Contratación *
            </label>
            <input v-model="form.fechaContratacion" type="date"
              style="width:100%;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);outline:none;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;box-sizing:border-box;"
              @focus="($event.target as HTMLInputElement).style.cssText+='border-color:#D4A8B0;background:white;box-shadow:0 0 0 3px rgba(212,168,176,0.15)'"
              @blur="($event.target as HTMLInputElement).style.cssText+='border-color:rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);box-shadow:none'"
              @input="formError=''"/>
          </div>

          <!-- Estado -->
          <div style="margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:10px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Estado
            </label>
            <div style="display:flex;gap:10px;">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 18px;border-radius:50px;border:1.5px solid;transition:all .2s;"
                :style="form.estado === 'activo' ? 'border-color:#3A7A50;background:rgba(134,188,149,0.1);' : 'border-color:rgba(212,168,176,0.4);background:white;'">
                <input type="radio" v-model="form.estado" value="activo" style="accent-color:#3A7A50;" />
                <span style="font-size:13px;font-weight:600;" :style="form.estado === 'activo' ? 'color:#3A7A50;' : 'color:#A896A0;'">Activo</span>
              </label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:10px 18px;border-radius:50px;border:1.5px solid;transition:all .2s;"
                :style="form.estado === 'inactivo' ? 'border-color:#B05070;background:rgba(176,80,112,0.08);' : 'border-color:rgba(212,168,176,0.4);background:white;'">
                <input type="radio" v-model="form.estado" value="inactivo" style="accent-color:#B05070;" />
                <span style="font-size:13px;font-weight:600;" :style="form.estado === 'inactivo' ? 'color:#B05070;' : 'color:#A896A0;'">Inactivo</span>
              </label>
            </div>
          </div>

          <!-- Contraseña -->
          <div style="margin-bottom:8px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:6px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              {{ action === 'crear' ? 'Contraseña *' : 'Nueva Contraseña (opcional)' }}
            </label>
            <div style="display:flex;align-items:center;gap:8px;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);">
              <input v-model="form.password" :type="showPwd ? 'text' : 'password'" placeholder="Mínimo 6 caracteres"
                style="border:none;outline:none;background:transparent;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;width:100%;"
                @input="formError=''"/>
              <button type="button" @click="showPwd=!showPwd" style="border:none;background:none;cursor:pointer;color:#C4A882;display:flex;flex-shrink:0;">
                <svg v-if="!showPwd" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            <p v-if="action==='editar'" style="font-size:10px;color:#A896A0;margin:4px 0 0 4px;">Dejar vacío para mantener la contraseña actual</p>
          </div>
        </div>

        <!-- Footer -->
        <div style="padding:16px 24px;border-top:1px solid rgba(212,168,176,0.2);display:flex;justify-content:flex-end;gap:10px;">
          <button @click="emit('close')" style="display:flex;align-items:center;gap:6px;padding:10px 20px;border:1.5px solid rgba(212,168,176,0.45);border-radius:50px;background:transparent;cursor:pointer;font-size:12px;font-weight:500;color:#A896A0;font-family:'Jost',sans-serif;">
            Cancelar
          </button>
          <button @click="guardar" style="display:flex;align-items:center;gap:6px;padding:10px 22px;border:none;border-radius:50px;cursor:pointer;font-size:12px;font-weight:600;color:white;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0 0%,#C4A882 50%,#7A4E38 100%);box-shadow:0 4px 14px rgba(122,78,56,0.25);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            {{ action === 'crear' ? 'Crear Empleado' : 'Guardar Cambios' }}
          </button>
        </div>
      </div>
    </div>
  </Transition>

  <!-- ── Modal Confirmar Eliminar ── -->
  <Transition name="nk-modal">
    <div v-if="showDelete"
      style="position:fixed;inset:0;background:rgba(90,55,35,0.4);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:200;padding:20px;font-family:'Jost',sans-serif;"
      @click.self="emit('closeDelete')">
      <div style="background:white;border-radius:24px;width:100%;max-width:400px;box-shadow:0 24px 64px rgba(122,78,56,0.25);overflow:hidden;">
        <div style="background:linear-gradient(135deg,#D47878,#C46060);padding:20px 24px;display:flex;align-items:center;gap:12px;">
          <div style="width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
          </div>
          <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:20px;font-weight:700;color:white;margin:0;">Confirmar eliminación</h3>
          <button @click="emit('closeDelete')" style="margin-left:auto;border:none;background:none;cursor:pointer;color:rgba(255,255,255,0.7);display:flex;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div style="padding:28px 24px;text-align:center;">
          <p style="color:#7A4E38;font-size:14px;margin:0 0 6px;">¿Estás segura de que deseas eliminar a</p>
          <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:18px;font-weight:700;color:#7A4E38;margin:0 0 10px;">{{ empleadoAEliminar?.nombre }}?</p>
          <p style="color:#A896A0;font-size:12px;margin:0;">Esta acción no se puede deshacer.</p>
        </div>
        <div style="padding:16px 24px;border-top:1px solid rgba(212,168,176,0.2);display:flex;justify-content:flex-end;gap:10px;">
          <button @click="emit('closeDelete')" style="display:flex;align-items:center;gap:6px;padding:10px 20px;border:1.5px solid rgba(212,168,176,0.45);border-radius:50px;background:transparent;cursor:pointer;font-size:12px;font-weight:500;color:#A896A0;font-family:'Jost',sans-serif;">
            Cancelar
          </button>
          <button @click="emit('eliminar')" style="display:flex;align-items:center;gap:6px;padding:10px 22px;border:none;border-radius:50px;cursor:pointer;font-size:12px;font-weight:600;color:white;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D47878,#C46060);box-shadow:0 4px 14px rgba(196,96,96,0.3);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style>
.nk-modal-enter-active, .nk-modal-leave-active { transition: all 0.22s ease; }
.nk-modal-enter-from, .nk-modal-leave-to { opacity: 0; }
.nk-modal-enter-from > div, .nk-modal-leave-to > div { transform: scale(0.95) translateY(12px); }
</style>