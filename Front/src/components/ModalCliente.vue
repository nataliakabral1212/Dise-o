<script setup lang="ts">
import { ref, watch } from 'vue'

export interface Cliente {
  id: number
  nombre: string
  correo: string
  telefono: string
  createdAt: string
}

interface ClienteForm {
  nombre: string
  correo: string
  telefono: string
  password: string
}

const props = defineProps<{
  show: boolean
  action: 'crear' | 'editar'
  cliente?: Cliente | null
  showDelete: boolean
  clienteAEliminar?: Cliente | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'closeDelete'): void
  (e: 'guardar', form: ClienteForm): void
  (e: 'eliminar'): void
}>()

const showPwd   = ref<boolean>(false)
const formError = ref<string>('')

function emptyForm(): ClienteForm {
  return { nombre: '', correo: '', telefono: '', password: '' }
}
const form = ref<ClienteForm>(emptyForm())

watch(() => props.cliente, (c) => {
  if (props.action === 'editar' && c) {
    form.value = { nombre: c.nombre, correo: c.correo, telefono: c.telefono, password: '' }
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

function guardar(): void {
  if (!form.value.nombre.trim())         { formError.value = 'El nombre es obligatorio.'; return }
  if (!form.value.correo.trim())         { formError.value = 'El correo es obligatorio.'; return }
  if (!form.value.telefono.trim())       { formError.value = 'El teléfono es obligatorio.'; return }
  if (form.value.telefono.length !== 10) { formError.value = 'El teléfono debe tener 10 dígitos.'; return }
  if (props.action === 'crear' && !form.value.password) { formError.value = 'La contraseña es obligatoria.'; return }
  if (form.value.password && form.value.password.length < 6) { formError.value = 'La contraseña debe tener mínimo 6 caracteres.'; return }
  emit('guardar', { ...form.value })
}
</script>

<template>
  <Transition name="nk-modal">
    <div v-if="show"
      style="position:fixed;inset:0;background:rgba(90,55,35,0.4);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:200;padding:20px;font-family:'Jost',sans-serif;"
      @click.self="emit('close')">
      <div style="background:white;border-radius:24px;width:100%;max-width:480px;box-shadow:0 24px 64px rgba(122,78,56,0.25);overflow:hidden;">

         <div style="padding:24px 24px 16px;display:flex;align-items:flex-start;justify-content:space-between;">
  <div style="flex:1;">
    <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:22px;font-weight:700;color:#7A4E38;margin:0;text-align:left;">
      {{ action === 'crear' ? 'Nuevo Cliente' : 'Editar Cliente' }}
    </h3>
    <p style="font-size:12px;color:#A896A0;margin:4px 0 0;text-align:left;">
      {{ action === 'crear' ? 'Completa los datos del cliente' : 'Actualizar información' }}
    </p>
  </div>
  <button @click="emit('close')" style="border:none;background:rgba(240,232,220,0.8);border-radius:50%;width:32px;height:32px;cursor:pointer;color:#A896A0;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-left:12px;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
</div>

        <div style="padding:24px 24px 16px;">
          <div v-if="formError" style="margin-bottom:14px;padding:10px 14px;border-radius:12px;background:rgba(212,120,120,0.1);border:1px solid rgba(212,120,120,0.25);color:#7A4E38;font-size:12px;text-align:center;">
            {{ formError }}
          </div>

          <div style="margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:5px;font-size:9px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A882;margin-bottom:6px;">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Nombre Completo *
            </label>
            <input v-model="form.nombre" type="text" placeholder="Ej: María López García"
              style="width:100%;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);outline:none;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;box-sizing:border-box;"
              @focus="($event.target as HTMLInputElement).style.cssText+='border-color:#D4A8B0;background:white;box-shadow:0 0 0 3px rgba(212,168,176,0.15)'"
              @blur="($event.target as HTMLInputElement).style.cssText+='border-color:rgba(212,168,176,0.3);background:rgba(240,232,220,0.35);box-shadow:none'"
              @input="formError=''"/>
          </div>

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

        <div style="padding:16px 24px;border-top:1px solid rgba(212,168,176,0.2);display:flex;justify-content:flex-end;gap:10px;">
          <button @click="emit('close')" style="display:flex;align-items:center;gap:6px;padding:10px 20px;border:1.5px solid rgba(212,168,176,0.45);border-radius:50px;background:transparent;cursor:pointer;font-size:12px;font-weight:500;color:#A896A0;font-family:'Jost',sans-serif;">
            Cancelar
          </button>
          <button @click="guardar" style="display:flex;align-items:center;gap:6px;padding:10px 22px;border:none;border-radius:50px;cursor:pointer;font-size:12px;font-weight:600;color:white;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0 0%,#C4A882 50%,#7A4E38 100%);box-shadow:0 4px 14px rgba(122,78,56,0.25);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            {{ action === 'crear' ? 'Crear Cliente' : 'Guardar Cambios' }}
          </button>
        </div>
      </div>
    </div>
  </Transition>

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
          <p style="font-family:'Cormorant Garamond',Georgia,serif;font-size:18px;font-weight:700;color:#7A4E38;margin:0 0 10px;">{{ clienteAEliminar?.nombre }}?</p>
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