<script setup lang="ts">
import { ref, watch, computed } from 'vue'

// ── Types ──────────────────────────────────────────────────────
export interface Servicio {
  id: number
  nombre: string
  duracion: number  // minutos
  precio: number
}

export interface Empleado {
  id: number
  nombre: string
  cargo: string
  servicios: Servicio[]
}

export interface Cliente {
  id: number
  nombre: string
  correo: string
  telefono: string
}

export interface ReservacionForm {
  clienteId: number | null
  empleadoId: number | null
  fecha: string
  horaInicio: string
  serviciosIds: number[]
  notas: string
  modo: 'salon' | 'empleado'
}

export interface Reservacion {
  id: number
  cliente: string
  empleado: string
  fecha: string
  horaInicio: string
  horaFin: string
  servicios: string[]
  total: number
  estado: 'pendiente' | 'confirmada' | 'completada' | 'cancelada' | 'no_asistio'
  notas: string
}

const props = defineProps<{
  show: boolean
  action: 'crear' | 'editar'
  reservacion?: Reservacion | null
  clientes: Cliente[]
  empleados: Empleado[]
  showDelete: boolean
  reservacionAEliminar?: Reservacion | null
  showCancel: boolean
  reservacionACancelar?: Reservacion | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'closeDelete'): void
  (e: 'closeCancel'): void
  (e: 'guardar', form: ReservacionForm): void
  (e: 'eliminar'): void
  (e: 'cancelar', motivo: string): void
}>()

const step = ref<1 | 2 | 3 | 4>(1)
const formError = ref('')
const motivoCancelacion = ref('')

function emptyForm(): ReservacionForm {
  return { clienteId: null, empleadoId: null, fecha: '', horaInicio: '', serviciosIds: [], notas: '', modo: 'salon' }
}
const form = ref<ReservacionForm>(emptyForm())

watch(() => props.show, (val) => {
  if (val) { formError.value = ''; step.value = 1; if (props.action === 'crear') form.value = emptyForm() }
})
watch(() => props.showCancel, (val) => { if (val) motivoCancelacion.value = '' })

// Todos los servicios únicos del salón
const todosLosServicios = computed<Servicio[]>(() => {
  const mapa = new Map<number, Servicio>()
  props.empleados.forEach(emp => emp.servicios.forEach(s => mapa.set(s.id, s)))
  return Array.from(mapa.values())
})

// Empleados que ofrecen TODOS los servicios seleccionados
const empleadosCompatibles = computed<Empleado[]>(() => {
  if (form.value.serviciosIds.length === 0) return props.empleados
  return props.empleados.filter(emp =>
    form.value.serviciosIds.every(sid => emp.servicios.some(s => s.id === sid))
  )
})

const empleadoSeleccionado = computed<Empleado | null>(() =>
  props.empleados.find(e => e.id === form.value.empleadoId) ?? null
)

const serviciosSeleccionados = computed<Servicio[]>(() =>
  todosLosServicios.value.filter(s => form.value.serviciosIds.includes(s.id))
)

const duracionTotal = computed<number>(() =>
  serviciosSeleccionados.value.reduce((acc, s) => acc + s.duracion, 0)
)

const totalPrecio = computed<number>(() =>
  serviciosSeleccionados.value.reduce((acc, s) => acc + s.precio, 0)
)

const horariosDisponibles = computed<string[]>(() => {
  const slots: string[] = []
  for (let h = 9; h < 19; h++) {
    slots.push(`${String(h).padStart(2,'0')}:00`)
    slots.push(`${String(h).padStart(2,'0')}:30`)
  }
  return slots
})

const horaFin = computed<string>(() => {
  if (!form.value.horaInicio || !duracionTotal.value) return ''
  const [h, m] = form.value.horaInicio.split(':').map(Number)
  const min = h * 60 + m + duracionTotal.value
  return `${String(Math.floor(min / 60)).padStart(2,'0')}:${String(min % 60).padStart(2,'0')}`
})

const clienteSeleccionado = computed<Cliente | null>(() =>
  props.clientes.find(c => c.id === form.value.clienteId) ?? null
)

const stepTitles: Record<number, string> = {
  1: 'Cliente & Fecha',
  2: 'Servicios',
  3: 'Empleado',
  4: 'Horario',
}

function nextStep() {
  formError.value = ''
  if (step.value === 1) {
    if (!form.value.clienteId) { formError.value = 'Selecciona un cliente.'; return }
    if (!form.value.fecha)     { formError.value = 'Selecciona una fecha.'; return }
    step.value = 2
  } else if (step.value === 2) {
    if (form.value.serviciosIds.length === 0) { formError.value = 'Selecciona al menos un servicio.'; return }
    if (form.value.empleadoId && !empleadosCompatibles.value.find(e => e.id === form.value.empleadoId)) {
      form.value.empleadoId = null
      form.value.modo = 'salon'
    }
    step.value = 3
  } else if (step.value === 3) {
    if (form.value.modo === 'empleado' && !form.value.empleadoId) {
      formError.value = 'Selecciona un empleado o elige que el salón lo asigne.'
      return
    }
    step.value = 4
  }
}

function prevStep() {
  formError.value = ''
  if (step.value > 1) step.value = (step.value - 1) as 1 | 2 | 3 | 4
}

function toggleServicio(id: number) {
  const idx = form.value.serviciosIds.indexOf(id)
  if (idx === -1) form.value.serviciosIds.push(id)
  else            form.value.serviciosIds.splice(idx, 1)
  formError.value = ''
}

function guardar() {
  if (!form.value.horaInicio) { formError.value = 'Selecciona un horario.'; return }
  emit('guardar', { ...form.value })
}

function fmtMoneda(n: number) {
  return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n)
}

const inputBase = "width:100%;padding:11px 14px;border:1.5px solid rgba(212,168,176,0.4);border-radius:12px;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;outline:none;box-sizing:border-box;background:#FDFAF7;"
</script>

<template>
  <Teleport to="body">

    <!-- ══ MODAL WIZARD ══ -->
    <Transition name="fade">
      <div v-if="show"
        style="position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:20px;"
        @click.self="emit('close')">

        <div style="position:absolute;inset:0;background:rgba(122,78,56,0.2);backdrop-filter:blur(5px);"></div>

        <div style="position:relative;background:white;border-radius:28px;width:100%;max-width:520px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 28px 70px rgba(122,78,56,0.22);font-family:'Jost',sans-serif;overflow:hidden;">

          <!-- Barra progreso -->
          <div style="height:3px;background:rgba(212,168,176,0.2);flex-shrink:0;">
            <div :style="`height:100%;background:linear-gradient(90deg,#D4A8B0,#C4A882,#7A4E38);transition:width 0.4s ease;width:${step * 25}%`"></div>
          </div>

          <!-- Header -->
          <div style="padding:24px 28px 14px;flex-shrink:0;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;">
              <div>
                <p style="font-size:10px;letter-spacing:0.2em;text-transform:uppercase;color:#C4A882;margin:0 0 3px;">
                  Paso {{ step }} de 4 · {{ stepTitles[step] }}
                </p>
                <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:23px;font-weight:700;color:#7A4E38;margin:0;">
                  {{ action === 'crear' ? 'Nueva Reservación' : 'Editar Reservación' }}
                </h3>
              </div>
              <button @click="emit('close')"
                style="width:34px;height:34px;border-radius:50%;border:none;background:#F0E8DC;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;margin-left:12px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7A4E38" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
            <!-- Steps bar -->
            <div style="display:flex;gap:6px;margin-top:14px;">
              <div v-for="s in 4" :key="s" style="flex:1;height:3px;border-radius:4px;transition:background 0.3s;"
                :style="s <= step ? 'background:linear-gradient(90deg,#D4A8B0,#C4A882)' : 'background:rgba(212,168,176,0.18)'"></div>
            </div>
          </div>

          <!-- Error -->
          <div v-if="formError" style="margin:0 28px 8px;padding:10px 14px;border-radius:12px;background:rgba(212,100,100,0.08);border:1px solid rgba(212,100,100,0.2);color:#8B3A3A;font-size:12px;text-align:center;flex-shrink:0;">
            {{ formError }}
          </div>

          <!-- Body -->
          <div style="padding:4px 28px 8px;overflow-y:auto;flex:1;">

            <!-- PASO 1: Cliente & Fecha -->
            <div v-if="step === 1" style="display:flex;flex-direction:column;gap:16px;padding-bottom:8px;">

              <div>
                <label style="font-size:10px;font-weight:600;color:#7A4E38;letter-spacing:0.15em;text-transform:uppercase;display:block;margin-bottom:8px;">Cliente *</label>
                <select v-model="form.clienteId" :style="inputBase" @change="formError=''">
                  <option :value="null" disabled>Selecciona un cliente...</option>
                  <option v-for="c in clientes" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                </select>
                <Transition name="slide">
                  <div v-if="clienteSeleccionado"
                    style="margin-top:8px;padding:10px 14px;background:rgba(240,232,220,0.6);border-radius:12px;display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#D4A8B0,#C4A882);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;flex-shrink:0;">
                      {{ clienteSeleccionado.nombre.charAt(0) }}
                    </div>
                    <div>
                      <p style="font-size:12px;font-weight:600;color:#7A4E38;margin:0;">{{ clienteSeleccionado.correo }}</p>
                      <p style="font-size:11px;color:#A896A0;margin:0;">{{ clienteSeleccionado.telefono }}</p>
                    </div>
                  </div>
                </Transition>
              </div>

              <div>
                <label style="font-size:10px;font-weight:600;color:#7A4E38;letter-spacing:0.15em;text-transform:uppercase;display:block;margin-bottom:8px;">Fecha *</label>
                <input v-model="form.fecha" type="date" :style="inputBase"
                  :min="new Date().toISOString().split('T')[0]"
                  @change="formError=''"
                  @focus="($event.target as HTMLInputElement).style.borderColor='#C4A882'"
                  @blur="($event.target as HTMLInputElement).style.borderColor='rgba(212,168,176,0.4)'" />
              </div>

              <div>
                <label style="font-size:10px;font-weight:600;color:#7A4E38;letter-spacing:0.15em;text-transform:uppercase;display:block;margin-bottom:8px;">
                  Notas <span style="font-weight:400;opacity:.6;">(opcional)</span>
                </label>
                <textarea v-model="form.notas" placeholder="Preferencias, alergias, observaciones..." rows="2"
                  :style="inputBase + 'resize:none;'"
                  @focus="($event.target as HTMLTextAreaElement).style.borderColor='#C4A882'"
                  @blur="($event.target as HTMLTextAreaElement).style.borderColor='rgba(212,168,176,0.4)'"></textarea>
              </div>
            </div>

            <!-- PASO 2: Servicios -->
            <div v-if="step === 2" style="display:flex;flex-direction:column;gap:12px;padding-bottom:8px;">

              <p style="font-size:12px;color:#A896A0;margin:0;">
                Elige uno o varios servicios. Después verás qué empleados pueden atenderte.
              </p>

              <div style="display:flex;flex-direction:column;gap:7px;">
                <div v-for="srv in todosLosServicios" :key="srv.id"
                  @click="toggleServicio(srv.id)"
                  style="display:flex;align-items:center;justify-content:space-between;padding:13px 14px;border-radius:14px;border:2px solid;cursor:pointer;transition:all 0.18s;"
                  :style="form.serviciosIds.includes(srv.id)
                    ? 'border-color:#C4A882;background:rgba(196,168,130,0.1);'
                    : 'border-color:rgba(212,168,176,0.25);background:#FDFAF7;'">
                  <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:20px;height:20px;border-radius:6px;border:2px solid;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all 0.18s;"
                      :style="form.serviciosIds.includes(srv.id)
                        ? 'border-color:#C4A882;background:linear-gradient(135deg,#C4A882,#7A4E38);'
                        : 'border-color:rgba(212,168,176,0.4);background:white;'">
                      <svg v-if="form.serviciosIds.includes(srv.id)" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                      <p style="font-size:13px;font-weight:600;color:#7A4E38;margin:0;">{{ srv.nombre }}</p>
                      <p style="font-size:11px;color:#A896A0;margin:0;">{{ srv.duracion }} min</p>
                    </div>
                  </div>
                  <span style="font-size:13px;font-weight:700;color:#7A4E38;flex-shrink:0;">{{ fmtMoneda(srv.precio) }}</span>
                </div>
              </div>

              <Transition name="slide">
                <div v-if="form.serviciosIds.length > 0"
                  style="padding:12px 16px;background:linear-gradient(135deg,rgba(212,168,176,0.15),rgba(196,168,130,0.15));border-radius:12px;display:flex;justify-content:space-between;align-items:center;">
                  <span style="font-size:12px;color:#7A4E38;">⏱ {{ duracionTotal }} min · {{ form.serviciosIds.length }} servicio{{ form.serviciosIds.length > 1 ? 's' : '' }}</span>
                  <span style="font-size:14px;font-weight:700;color:#7A4E38;">{{ fmtMoneda(totalPrecio) }}</span>
                </div>
              </Transition>
            </div>

            <!-- PASO 3: Empleado -->
            <div v-if="step === 3" style="display:flex;flex-direction:column;gap:12px;padding-bottom:8px;">

              <!-- Tarjetas modo -->
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div @click="form.modo='salon';form.empleadoId=null"
                  style="padding:16px 12px;border-radius:16px;border:2px solid;cursor:pointer;transition:all 0.2s;text-align:center;"
                  :style="form.modo==='salon' ? 'border-color:#C4A882;background:rgba(196,168,130,0.1);' : 'border-color:rgba(212,168,176,0.25);background:#FDFAF7;'">
                  <div style="width:40px;height:40px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;"
                    :style="form.modo==='salon' ? 'background:linear-gradient(135deg,#D4A8B0,#C4A882);' : 'background:rgba(212,168,176,0.12);'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" :stroke="form.modo==='salon' ? 'white' : '#C4A882'" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  </div>
                  <p style="font-size:12px;font-weight:700;margin:0 0 3px;" :style="form.modo==='salon' ? 'color:#7A4E38;' : 'color:#A896A0;'">El salón elige</p>
                  <p style="font-size:10px;line-height:1.3;margin:0;color:#C4A882;" :style="form.modo!=='salon' ? 'opacity:0.5;' : ''">Te asignamos al empleado más disponible</p>
                </div>

                <div @click="form.modo='empleado'"
                  style="padding:16px 12px;border-radius:16px;border:2px solid;cursor:pointer;transition:all 0.2s;text-align:center;"
                  :style="form.modo==='empleado' ? 'border-color:#C4A882;background:rgba(196,168,130,0.1);' : 'border-color:rgba(212,168,176,0.25);background:#FDFAF7;'">
                  <div style="width:40px;height:40px;border-radius:50%;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;"
                    :style="form.modo==='empleado' ? 'background:linear-gradient(135deg,#D4A8B0,#C4A882);' : 'background:rgba(212,168,176,0.12);'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" :stroke="form.modo==='empleado' ? 'white' : '#C4A882'" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  </div>
                  <p style="font-size:12px;font-weight:700;margin:0 0 3px;" :style="form.modo==='empleado' ? 'color:#7A4E38;' : 'color:#A896A0;'">Yo elijo</p>
                  <p style="font-size:10px;line-height:1.3;margin:0;color:#C4A882;" :style="form.modo!=='empleado' ? 'opacity:0.5;' : ''">Elige con quién quieres tu cita</p>
                </div>
              </div>

              <!-- Lista empleados compatibles -->
              <Transition name="slide">
                <div v-if="form.modo === 'empleado'" style="display:flex;flex-direction:column;gap:7px;">

                  <p v-if="empleadosCompatibles.length === 0"
                    style="text-align:center;padding:20px;background:rgba(240,232,220,0.4);border-radius:12px;color:#A896A0;font-size:13px;margin:0;">
                    Ningún empleado ofrece todos los servicios seleccionados.
                  </p>

                  <template v-else>
                    <p style="font-size:10px;font-weight:600;color:#C4A882;letter-spacing:0.12em;text-transform:uppercase;margin:0;">
                      {{ empleadosCompatibles.length }} empleado{{ empleadosCompatibles.length !== 1 ? 's' : '' }} disponible{{ empleadosCompatibles.length !== 1 ? 's' : '' }} para estos servicios
                    </p>

                    <div v-for="emp in empleadosCompatibles" :key="emp.id"
                      @click="form.empleadoId = emp.id; formError = ''"
                      style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:14px;border:2px solid;cursor:pointer;transition:all 0.18s;"
                      :style="form.empleadoId === emp.id
                        ? 'border-color:#C4A882;background:rgba(196,168,130,0.1);'
                        : 'border-color:rgba(212,168,176,0.25);background:#FDFAF7;'">
                      <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#D4A8B0,#C4A882);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:16px;flex-shrink:0;">
                        {{ emp.nombre.charAt(0) }}
                      </div>
                      <div style="flex:1;min-width:0;">
                        <p style="font-size:13px;font-weight:600;color:#7A4E38;margin:0;">{{ emp.nombre }}</p>
                        <p style="font-size:11px;color:#A896A0;margin:2px 0 5px;">{{ emp.cargo }}</p>
                        <div style="display:flex;flex-wrap:wrap;gap:3px;">
                          <span v-for="sid in form.serviciosIds" :key="sid"
                            style="padding:2px 7px;border-radius:50px;font-size:10px;font-weight:600;background:rgba(196,168,130,0.15);color:#8B6B3D;">
                            {{ todosLosServicios.find(s=>s.id===sid)?.nombre }}
                          </span>
                        </div>
                      </div>
                      <div v-if="form.empleadoId === emp.id"
                        style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#C4A882,#7A4E38);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                      </div>
                    </div>
                  </template>
                </div>
              </Transition>
            </div>

            <!-- PASO 4: Horario -->
            <div v-if="step === 4" style="display:flex;flex-direction:column;gap:14px;padding-bottom:8px;">

              <!-- Resumen -->
              <div style="padding:14px 16px;background:rgba(240,232,220,0.5);border-radius:16px;border:1px solid rgba(212,168,176,0.2);">
                <p style="font-size:10px;font-weight:600;color:#C4A882;letter-spacing:0.12em;text-transform:uppercase;margin:0 0 10px;">Resumen de la cita</p>
                <div style="display:flex;flex-direction:column;gap:6px;">
                  <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:#A896A0;">Cliente</span>
                    <span style="color:#7A4E38;font-weight:600;">{{ clienteSeleccionado?.nombre }}</span>
                  </div>
                  <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:#A896A0;">Fecha</span>
                    <span style="color:#7A4E38;font-weight:600;">{{ new Date(form.fecha + 'T12:00:00').toLocaleDateString('es-MX',{weekday:'short',day:'2-digit',month:'short',year:'numeric'}) }}</span>
                  </div>
                  <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:#A896A0;">Empleado</span>
                    <span v-if="form.modo==='empleado' && empleadoSeleccionado" style="color:#7A4E38;font-weight:600;">{{ empleadoSeleccionado.nombre }}</span>
                    <span v-else style="color:#C4A882;font-style:italic;font-size:11px;">Asignado por el salón</span>
                  </div>
                  <div style="display:flex;justify-content:space-between;font-size:12px;align-items:flex-start;">
                    <span style="color:#A896A0;flex-shrink:0;">Servicios</span>
                    <span style="color:#7A4E38;font-weight:600;text-align:right;max-width:65%;">{{ serviciosSeleccionados.map(s=>s.nombre).join(', ') }}</span>
                  </div>
                  <div style="border-top:1px solid rgba(212,168,176,0.2);margin:2px 0;"></div>
                  <div style="display:flex;justify-content:space-between;font-size:13px;">
                    <span style="color:#A896A0;">Total estimado</span>
                    <span style="color:#7A4E38;font-weight:700;">{{ fmtMoneda(totalPrecio) }}</span>
                  </div>
                </div>
              </div>

              <!-- Grid horarios -->
              <div>
                <label style="font-size:10px;font-weight:600;color:#7A4E38;letter-spacing:0.15em;text-transform:uppercase;display:block;margin-bottom:8px;">
                  Hora de inicio *
                  <span v-if="form.horaInicio" style="font-weight:500;color:#C4A882;text-transform:none;letter-spacing:0;">
                    · {{ form.horaInicio }} → {{ horaFin }} ({{ duracionTotal }} min)
                  </span>
                </label>
                <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px;">
                  <button v-for="slot in horariosDisponibles" :key="slot"
                    @click="form.horaInicio=slot;formError=''"
                    style="padding:9px 4px;border-radius:10px;border:2px solid;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.18s;font-family:'Jost',sans-serif;"
                    :style="form.horaInicio===slot
                      ? 'border-color:#C4A882;background:linear-gradient(135deg,#D4A8B0,#C4A882);color:white;box-shadow:0 3px 8px rgba(196,168,130,0.4);'
                      : 'border-color:rgba(212,168,176,0.3);background:#FDFAF7;color:#7A4E38;'">
                    {{ slot }}
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Footer -->
          <div style="padding:16px 28px 22px;border-top:1px solid rgba(212,168,176,0.12);display:flex;gap:10px;flex-shrink:0;">
            <button v-if="step > 1" @click="prevStep"
              style="padding:12px 18px;border:1.5px solid rgba(212,168,176,0.45);border-radius:50px;background:white;font-size:13px;font-weight:600;color:#A896A0;cursor:pointer;font-family:'Jost',sans-serif;display:flex;align-items:center;gap:5px;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
              Atrás
            </button>
            <button v-if="step < 4" @click="nextStep"
              style="flex:1;padding:12px;border:none;border-radius:50px;font-size:13px;font-weight:600;color:white;cursor:pointer;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0 0%,#C4A882 50%,#7A4E38 100%);box-shadow:0 6px 18px rgba(122,78,56,0.22);display:flex;align-items:center;justify-content:center;gap:5px;">
              Siguiente
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <button v-if="step === 4" @click="guardar"
              style="flex:1;padding:12px;border:none;border-radius:50px;font-size:13px;font-weight:600;color:white;cursor:pointer;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0 0%,#C4A882 50%,#7A4E38 100%);box-shadow:0 6px 18px rgba(122,78,56,0.22);display:flex;align-items:center;justify-content:center;gap:6px;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Confirmar Reservación
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ══ MODAL CANCELAR ══ -->
    <Transition name="fade">
      <div v-if="showCancel"
        style="position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:20px;"
        @click.self="emit('closeCancel')">
        <div style="position:absolute;inset:0;background:rgba(122,78,56,0.2);backdrop-filter:blur(5px);"></div>
        <div style="position:relative;background:white;border-radius:24px;padding:36px;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(122,78,56,0.2);font-family:'Jost',sans-serif;text-align:center;">
          <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,235,220,0.9);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          </div>
          <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:22px;font-weight:700;color:#7A4E38;margin:0 0 6px;">Cancelar Reservación</h3>
          <p style="font-size:13px;color:#A896A0;margin:0 0 20px;">
            Estás cancelando la cita de <strong style="color:#7A4E38;">{{ reservacionACancelar?.cliente }}</strong>
          </p>
          <div style="text-align:left;margin-bottom:20px;">
            <label style="font-size:10px;font-weight:600;color:#7A4E38;letter-spacing:0.15em;text-transform:uppercase;display:block;margin-bottom:8px;">Motivo <span style="font-weight:400;opacity:.6;">(opcional)</span></label>
            <textarea v-model="motivoCancelacion" placeholder="¿Por qué se cancela?" rows="3"
              style="width:100%;padding:11px 14px;border:1.5px solid rgba(212,168,176,0.4);border-radius:12px;font-size:13px;color:#7A4E38;font-family:'Jost',sans-serif;outline:none;box-sizing:border-box;background:#FDFAF7;resize:none;"></textarea>
          </div>
          <div style="display:flex;gap:10px;">
            <button @click="emit('closeCancel')" style="flex:1;padding:12px;border:1.5px solid rgba(212,168,176,0.45);border-radius:50px;background:white;font-size:13px;font-weight:600;color:#A896A0;cursor:pointer;font-family:'Jost',sans-serif;">Volver</button>
            <button @click="emit('cancelar', motivoCancelacion)" style="flex:1;padding:12px;border:none;border-radius:50px;font-size:13px;font-weight:600;color:white;cursor:pointer;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0,#C46060);box-shadow:0 6px 18px rgba(196,96,96,0.28);">Sí, cancelar</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ══ MODAL ELIMINAR ══ -->
    <Transition name="fade">
      <div v-if="showDelete"
        style="position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:20px;"
        @click.self="emit('closeDelete')">
        <div style="position:absolute;inset:0;background:rgba(122,78,56,0.2);backdrop-filter:blur(5px);"></div>
        <div style="position:relative;background:white;border-radius:24px;padding:36px;width:100%;max-width:400px;box-shadow:0 24px 60px rgba(122,78,56,0.2);font-family:'Jost',sans-serif;text-align:center;">
          <div style="width:56px;height:56px;border-radius:50%;background:#FFF0F3;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#B05070" stroke-width="2.5" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
          </div>
          <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:22px;font-weight:700;color:#7A4E38;margin:0 0 8px;">Eliminar Reservación</h3>
          <p style="font-size:13px;color:#A896A0;margin:0 0 28px;">
            ¿Eliminar la reservación de <strong style="color:#7A4E38;">{{ reservacionAEliminar?.cliente }}</strong>? Esta acción no se puede deshacer.
          </p>
          <div style="display:flex;gap:10px;">
            <button @click="emit('closeDelete')" style="flex:1;padding:12px;border:1.5px solid rgba(212,168,176,0.45);border-radius:50px;background:white;font-size:13px;font-weight:600;color:#A896A0;cursor:pointer;font-family:'Jost',sans-serif;">Cancelar</button>
            <button @click="emit('eliminar')" style="flex:1;padding:12px;border:none;border-radius:50px;font-size:13px;font-weight:600;color:white;cursor:pointer;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#D4A8B0,#B05070);box-shadow:0 6px 18px rgba(176,80,112,0.28);">Sí, eliminar</button>
          </div>
        </div>
      </div>
    </Transition>

  </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .22s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: all .22s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-6px); }
</style>