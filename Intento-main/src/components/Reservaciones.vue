<script setup lang="ts">
const emit = defineEmits<{ (e: 'navigate', route: string): void }>()
import { ref, computed } from 'vue'
import MenuLateral from './MenuLateral.vue'
import ModalReservacion from './ModalReservacion.vue'
import type { Reservacion, ReservacionForm, Cliente, Empleado } from './ModalReservacion.vue'

// ── Datos de ejemplo ───────────────────────────────────────────
const clientes = ref<Cliente[]>([
  { id: 1, nombre: 'María López García',     correo: 'maria@gmail.com', telefono: '8711234567' },
  { id: 2, nombre: 'Juan Pérez Martínez',    correo: 'juan@gmail.com',  telefono: '8717654321' },
  { id: 3, nombre: 'Ana Rodríguez Sánchez',  correo: 'ana@gmail.com',   telefono: '8719876543' },
])

const empleados = ref<Empleado[]>([
  {
    id: 1, nombre: 'Sofía González', cargo: 'Estilista',
    servicios: [
      { id: 1, nombre: 'Corte de cabello', duracion: 30, precio: 180 },
      { id: 2, nombre: 'Tinte completo',   duracion: 90, precio: 650 },
      { id: 3, nombre: 'Peinado',          duracion: 45, precio: 250 },
    ]
  },
  {
    id: 2, nombre: 'Laura Martínez', cargo: 'Manicurista',
    servicios: [
      { id: 4, nombre: 'Manicure clásico', duracion: 40, precio: 150 },
      { id: 5, nombre: 'Uñas acrílicas',   duracion: 90, precio: 450 },
    ]
  },
  {
    id: 3, nombre: 'Karla Díaz', cargo: 'Esteticista',
    servicios: [
      { id: 6, nombre: 'Facial básico',    duracion: 60, precio: 380 },
      { id: 7, nombre: 'Depilación cejas', duracion: 20, precio: 120 },
    ]
  },
])

const reservaciones = ref<Reservacion[]>([
  { id: 1, cliente: 'María López García', empleado: 'Sofía González',  fecha: '2025-07-10', horaInicio: '10:00', horaFin: '11:30', servicios: ['Corte de cabello','Tinte completo'], total: 830,  estado: 'confirmada',  notas: '' },
  { id: 2, cliente: 'Juan Pérez Martínez',   empleado: 'Laura Martínez',  fecha: '2025-07-11', horaInicio: '09:00', horaFin: '09:40', servicios: ['Manicure clásico'],                  total: 150,  estado: 'pendiente',   notas: 'Sin gel' },
  { id: 3, cliente: 'Ana Rodríguez Sánchez', empleado: 'Karla Díaz',      fecha: '2025-07-09', horaInicio: '15:00', horaFin: '16:00', servicios: ['Facial básico'],                     total: 380,  estado: 'completada',  notas: '' },
  { id: 4, cliente: 'María López García',    empleado: 'Karla Díaz',      fecha: '2025-07-08', horaInicio: '11:00', horaFin: '11:20', servicios: ['Depilación cejas'],                  total: 120,  estado: 'cancelada',   notas: '' },
  { id: 5, cliente: 'Juan Pérez Martínez',   empleado: 'Sofía González',  fecha: '2025-07-12', horaInicio: '14:00', horaFin: '14:45', servicios: ['Peinado'],                            total: 250,  estado: 'pendiente',   notas: 'Evento especial' },
])

// ── State ──────────────────────────────────────────────────────
const busqueda          = ref('')
const filtroEstado      = ref<string>('todos')
const showModal         = ref(false)
const modalAction       = ref<'crear'|'editar'>('crear')
const selectedRes       = ref<Reservacion|null>(null)
const showDeleteModal   = ref(false)
const resAEliminar      = ref<Reservacion|null>(null)
const showCancelModal   = ref(false)
const resACancelar      = ref<Reservacion|null>(null)

// ── Computed ───────────────────────────────────────────────────
const estadoOpciones = [
  { value: 'todos',      label: 'Todas',      color: '#C4A882' },
  { value: 'pendiente',  label: 'Pendientes', color: '#C4A882' },
  { value: 'confirmada', label: 'Confirmadas',color: '#3A7A50' },
  { value: 'completada', label: 'Completadas',color: '#5A7AB0' },
  { value: 'cancelada',  label: 'Canceladas', color: '#B05070' },
  { value: 'no_asistio', label: 'No asistió', color: '#A896A0' },
]

const resFiltradas = computed(() => {
  let lista = reservaciones.value
  if (filtroEstado.value !== 'todos') lista = lista.filter(r => r.estado === filtroEstado.value)
  if (busqueda.value) {
    const t = busqueda.value.toLowerCase()
    lista = lista.filter(r =>
      r.cliente.toLowerCase().includes(t) ||
      r.empleado.toLowerCase().includes(t) ||
      r.servicios.some(s => s.toLowerCase().includes(t))
    )
  }
  return lista
})

const contadores = computed(() => ({
  todos:      reservaciones.value.length,
  pendiente:  reservaciones.value.filter(r => r.estado === 'pendiente').length,
  confirmada: reservaciones.value.filter(r => r.estado === 'confirmada').length,
  completada: reservaciones.value.filter(r => r.estado === 'completada').length,
  cancelada:  reservaciones.value.filter(r => r.estado === 'cancelada').length,
}))

// ── Methods ────────────────────────────────────────────────────
function openCrear() {
  modalAction.value = 'crear'
  selectedRes.value = null
  showModal.value = true
}

function onGuardar(form: ReservacionForm) {
  const cliente  = clientes.value.find(c => c.id === form.clienteId)
  const empleado = empleados.value.find(e => e.id === form.empleadoId)

  // calcular hora fin
  const serviciosSel = empleado
    ? empleado.servicios.filter(s => form.serviciosIds.includes(s.id))
    : empleados.value.flatMap(e => e.servicios).filter(s => form.serviciosIds.includes(s.id))
  const durTotal = serviciosSel.reduce((a, s) => a + s.duracion, 0)
  const total    = serviciosSel.reduce((a, s) => a + s.precio, 0)
  const [h, m]   = form.horaInicio.split(':').map(Number)
  const minFin   = h * 60 + m + durTotal
  const horaFin  = `${String(Math.floor(minFin/60)).padStart(2,'0')}:${String(minFin%60).padStart(2,'0')}`

  reservaciones.value.push({
    id: Date.now(),
    cliente:    cliente?.nombre ?? 'Cliente',
    empleado:   empleado?.nombre ?? 'Por asignar',
    fecha:      form.fecha,
    horaInicio: form.horaInicio,
    horaFin,
    servicios:  serviciosSel.map(s => s.nombre),
    total,
    estado:     'pendiente',
    notas:      form.notas,
  })
  showModal.value = false
}

function pedirCancelar(r: Reservacion) {
  resACancelar.value = r
  showCancelModal.value = true
}

function onCancelar(motivo: string) {
  if (resACancelar.value) {
    const idx = reservaciones.value.findIndex(r => r.id === resACancelar.value!.id)
    if (idx !== -1) reservaciones.value[idx] = { ...reservaciones.value[idx]!, estado: 'cancelada' }
  }
  showCancelModal.value = false
  resACancelar.value = null
}

function pedirEliminar(r: Reservacion) {
  resAEliminar.value = r
  showDeleteModal.value = true
}

function onEliminar() {
  if (resAEliminar.value)
    reservaciones.value = reservaciones.value.filter(r => r.id !== resAEliminar.value!.id)
  showDeleteModal.value = false
  resAEliminar.value = null
}

// ── Helpers ────────────────────────────────────────────────────
const estadoConfig: Record<string, { label: string; bg: string; color: string }> = {
  pendiente:  { label: 'Pendiente',   bg: 'rgba(196,168,130,0.12)', color: '#8B6B3D' },
  confirmada: { label: 'Confirmada',  bg: 'rgba(100,180,130,0.12)', color: '#3A7A50' },
  completada: { label: 'Completada',  bg: 'rgba(90,122,176,0.12)',  color: '#3A5A8A' },
  cancelada:  { label: 'Cancelada',   bg: 'rgba(176,80,112,0.10)',  color: '#B05070' },
  no_asistio: { label: 'No asistió',  bg: 'rgba(168,150,160,0.12)', color: '#7A6070' },
}

function fmtFecha(f: string) {
  return new Date(f + 'T12:00:00').toLocaleDateString('es-MX', { day:'2-digit', month:'short', year:'numeric' })
}
function fmtMoneda(n: number) {
  return new Intl.NumberFormat('es-MX', { style:'currency', currency:'MXN' }).format(n)
}
function esCancelable(estado: string) {
  return !['cancelada','completada','no_asistio'].includes(estado)
}
</script>

<template>
  <div style="position:fixed;inset:0;display:flex;font-family:'Jost',sans-serif;background:#f0e8dc;overflow:hidden;">

    <!-- BG floral sutil -->
    <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
      <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;width:100%;height:100%" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
        <ellipse cx="1380" cy="820" rx="50" ry="120" fill="#C4A882" opacity="0.11" transform="rotate(15,1380,820)"/>
        <ellipse cx="1360" cy="40"  rx="35" ry="85"  fill="#D47878" opacity="0.10" transform="rotate(40,1360,40)"/>
        <ellipse cx="40"   cy="880" rx="45" ry="110" fill="#D4A8B0" opacity="0.12" transform="rotate(-30,40,880)"/>
      </svg>
    </div>

    <!-- Sidebar -->
    <MenuLateral active-route="reservaciones" @navigate="emit('navigate', $event)" />

    <!-- Main -->
    <main style="flex:1;display:flex;flex-direction:column;padding:28px 32px;min-width:0;position:relative;z-index:5;overflow:hidden;">

      <!-- Topbar -->
      <header style="display:flex;align-items:flex-start;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <div>
          <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:30px;font-weight:700;color:#7a4e38;margin:0;line-height:1.1;">
            Reservaciones
          </h2>
          <p style="font-size:12px;color:#a896a0;margin:3px 0 0;">
            {{ reservaciones.length }} reservaciones · {{ contadores.pendiente }} pendientes hoy
          </p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;margin-left:auto;flex-wrap:wrap;">
          <!-- Buscador -->
          <div style="display:flex;align-items:center;gap:8px;background:white;border:1.5px solid rgba(212,168,176,0.4);border-radius:50px;padding:8px 16px;box-shadow:0 2px 8px rgba(122,78,56,0.06);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input v-model="busqueda" type="text" placeholder="Buscar reservación..."
              style="border:none;outline:none;background:transparent;font-size:13px;color:#7a4e38;font-family:'Jost',sans-serif;width:170px;"/>
          </div>
          <!-- Nueva -->
          <button @click="openCrear"
            style="display:flex;align-items:center;gap:7px;padding:10px 22px;border:none;border-radius:50px;cursor:pointer;font-size:12px;font-weight:600;letter-spacing:0.05em;color:white;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#d4a8b0 0%,#c4a882 50%,#7a4e38 100%);box-shadow:0 6px 18px rgba(122,78,56,0.25);white-space:nowrap;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Reservación
          </button>
        </div>
      </header>

      <!-- Filtros de estado -->
      <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
        <button v-for="op in estadoOpciones" :key="op.value"
          @click="filtroEstado=op.value"
          style="padding:7px 16px;border-radius:50px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;font-family:'Jost',sans-serif;border:1.5px solid;"
          :style="filtroEstado===op.value
            ? `border-color:${op.color};background:${op.color};color:white;`
            : 'border-color:rgba(212,168,176,0.35);background:white;color:#A896A0;'">
          {{ op.label }}
          <span style="margin-left:4px;font-size:11px;opacity:0.75;">
            ({{ op.value === 'todos' ? contadores.todos : (contadores as any)[op.value] ?? 0 }})
          </span>
        </button>
      </div>

      <!-- Tabla -->
      <div style="flex:1;background:white;border-radius:20px;box-shadow:0 8px 40px rgba(122,78,56,0.1);overflow:auto;">

        <!-- Empty state -->
        <div v-if="resFiltradas.length === 0"
          style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;color:#a896a0;">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D4A8B0" stroke-width="1.2" style="margin-bottom:12px">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          <p style="font-size:14px;">No se encontraron reservaciones</p>
        </div>

        <table v-else style="width:100%;border-collapse:collapse;font-size:13px;">
          <colgroup>
            <col style="width:17%"/>
            <col style="width:14%"/>
            <col style="width:12%"/>
            <col style="width:10%"/>
            <col style="width:22%"/>
            <col style="width:10%"/>
            <col style="width:10%"/>
            <col style="width:5%"/>
          </colgroup>
          <thead>
            <tr style="background:linear-gradient(135deg,#d4a8b0 0%,#c4a882 55%,#9a7a5a 100%);">
              <th style="padding:14px 16px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Cliente</th>
              <th style="padding:14px 16px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Empleado</th>
              <th style="padding:14px 16px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Fecha</th>
              <th style="padding:14px 16px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Hora</th>
              <th style="padding:14px 16px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Servicios</th>
              <th style="padding:14px 16px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Total</th>
              <th style="padding:14px 16px;text-align:center;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Estado</th>
              <th style="padding:14px 16px;text-align:center;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">···</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in resFiltradas" :key="r.id"
              style="border-bottom:1px solid rgba(212,168,176,0.15);transition:background 0.15s;"
              @mouseenter="($event.currentTarget as HTMLElement).style.background='rgba(240,232,220,0.4)'"
              @mouseleave="($event.currentTarget as HTMLElement).style.background='transparent'">

              <!-- Cliente -->
              <td style="padding:14px 16px;vertical-align:middle;">
                <div style="display:flex;align-items:center;gap:10px;">
                  <div style="width:32px;height:32px;min-width:32px;border-radius:50%;background:linear-gradient(135deg,#d4a8b0,#c4a882);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;">
                    {{ r.cliente.charAt(0) }}
                  </div>
                  <span style="font-weight:600;color:#7a4e38;font-size:12px;">{{ r.cliente }}</span>
                </div>
              </td>

              <!-- Empleado -->
              <td style="padding:14px 16px;vertical-align:middle;">
                <span v-if="r.empleado !== 'Por asignar'" style="font-size:12px;color:#5a4040;">{{ r.empleado }}</span>
                <span v-else style="font-size:11px;color:#C4A882;font-style:italic;">Por asignar</span>
              </td>

              <!-- Fecha -->
              <td style="padding:14px 16px;vertical-align:middle;color:#5a4040;font-size:12px;">{{ fmtFecha(r.fecha) }}</td>

              <!-- Hora -->
              <td style="padding:14px 16px;vertical-align:middle;">
                <div style="font-size:12px;font-weight:600;color:#7a4e38;">{{ r.horaInicio }}</div>
                <div style="font-size:10px;color:#A896A0;">→ {{ r.horaFin }}</div>
              </td>

              <!-- Servicios -->
              <td style="padding:14px 16px;vertical-align:middle;">
                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                  <span v-for="(s, i) in r.servicios.slice(0, 2)" :key="i"
                    style="padding:3px 8px;border-radius:50px;background:rgba(196,168,130,0.12);color:#7a4e38;font-size:10px;font-weight:600;">
                    {{ s }}
                  </span>
                  <span v-if="r.servicios.length > 2"
                    style="padding:3px 8px;border-radius:50px;background:rgba(212,168,176,0.15);color:#A896A0;font-size:10px;">
                    +{{ r.servicios.length - 2 }}
                  </span>
                </div>
              </td>

              <!-- Total -->
              <td style="padding:14px 16px;vertical-align:middle;font-size:12px;font-weight:700;color:#7a4e38;">
                {{ fmtMoneda(r.total) }}
              </td>

              <!-- Estado -->
              <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                <span :style="`padding:4px 10px;border-radius:50px;font-size:10px;font-weight:700;background:${estadoConfig[r.estado]?.bg};color:${estadoConfig[r.estado]?.color};`">
                  {{ estadoConfig[r.estado]?.label }}
                </span>
              </td>

              <!-- Acciones -->
              <td style="padding:14px 16px;vertical-align:middle;">
                <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                  <!-- Cancelar -->
                  <button v-if="esCancelable(r.estado)"
                    @click="pedirCancelar(r)" title="Cancelar"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:rgba(196,168,130,0.15);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;"
                    @mouseenter="($event.currentTarget as HTMLElement).style.background='rgba(196,96,60,0.15)'"
                    @mouseleave="($event.currentTarget as HTMLElement).style.background='rgba(196,168,130,0.15)'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                  </button>
                  <!-- Eliminar -->
                  <button @click="pedirEliminar(r)" title="Eliminar"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:rgba(212,168,176,0.15);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;"
                    @mouseenter="($event.currentTarget as HTMLElement).style.background='rgba(176,80,112,0.2)'"
                    @mouseleave="($event.currentTarget as HTMLElement).style.background='rgba(212,168,176,0.15)'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#D4A8B0" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- Modal -->
    <ModalReservacion
      :show="showModal"
      :action="modalAction"
      :reservacion="selectedRes"
      :clientes="clientes"
      :empleados="empleados"
      :show-delete="showDeleteModal"
      :reservacion-a-eliminar="resAEliminar"
      :show-cancel="showCancelModal"
      :reservacion-a-cancelar="resACancelar"
      @close="showModal=false"
      @close-delete="showDeleteModal=false"
      @close-cancel="showCancelModal=false"
      @guardar="onGuardar"
      @eliminar="onEliminar"
      @cancelar="onCancelar"
    />
  </div>
</template>