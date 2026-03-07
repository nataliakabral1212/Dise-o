<script setup lang="ts">
const emit = defineEmits<{ (e: 'navigate', route: string): void }>()
import { ref, computed } from "vue";
import MenuLateral from "./MenuLateral.vue";
import ModalEmpleado from "./ModalEmpleado.vue";
import type { Empleado } from "./ModalEmpleado.vue";

interface EmpleadoForm {
  nombre: string;
  correo: string;
  telefono: string;
  cargo: string;
  estado: 'activo' | 'inactivo';
  fechaContratacion: string;
  password: string;
}

// ── State ──────────────────────────────────────────────────────
const empleados = ref<Empleado[]>([
  {
    id: 1,
    nombre: "Sofía González Ruiz",
    correo: "sofia@gmail.com",
    telefono: "8711234567",
    cargo: "Estilista",
    estado: "activo",
    fechaContratacion: "2023-03-10",
  },
  {
    id: 2,
    nombre: "Laura Martínez Torres",
    correo: "laura@gmail.com",
    telefono: "8717654321",
    cargo: "Manicurista",
    estado: "activo",
    fechaContratacion: "2023-06-15",
  },
  {
    id: 3,
    nombre: "Karla Díaz Hernández",
    correo: "karla@gmail.com",
    telefono: "8719876543",
    cargo: "Esteticista",
    estado: "inactivo",
    fechaContratacion: "2022-11-01",
  },
]);

const busqueda = ref<string>("");
const showModal = ref<boolean>(false);
const modalAction = ref<"crear" | "editar">("crear");
const showDeleteModal = ref<boolean>(false);
const empleadoAEliminar = ref<Empleado | null>(null);
const selectedEmpleado = ref<Empleado | null>(null);

// ── Computed ───────────────────────────────────────────────────
const empleadosFiltrados = computed<Empleado[]>(() => {
  if (!busqueda.value) return empleados.value;
  const t = busqueda.value.toLowerCase();
  return empleados.value.filter(
    (e: Empleado) =>
      e.nombre.toLowerCase().includes(t) ||
      e.correo.toLowerCase().includes(t) ||
      e.cargo.toLowerCase().includes(t) ||
      String(e.telefono).includes(t),
  );
});

const totalActivos = computed(() => empleados.value.filter(e => e.estado === 'activo').length);

// ── Methods ────────────────────────────────────────────────────
function openCrear(): void {
  modalAction.value = "crear";
  selectedEmpleado.value = null;
  showModal.value = true;
}

function openEditar(e: Empleado): void {
  modalAction.value = "editar";
  selectedEmpleado.value = { ...e };
  showModal.value = true;
}

function onGuardar(form: EmpleadoForm): void {
  if (modalAction.value === "crear") {
    empleados.value.push({
      id: Date.now(),
      nombre: form.nombre,
      correo: form.correo,
      telefono: form.telefono,
      cargo: form.cargo,
      estado: form.estado,
      fechaContratacion: form.fechaContratacion,
    });
  } else {
    const idx = empleados.value.findIndex(
      (e: Empleado) => e.id === selectedEmpleado.value?.id,
    );
    if (idx !== -1) {
      const original = empleados.value[idx]!;
      empleados.value[idx] = {
        id: original.id,
        nombre: form.nombre,
        correo: form.correo,
        telefono: form.telefono,
        cargo: form.cargo,
        estado: form.estado,
        fechaContratacion: form.fechaContratacion,
      };
    }
  }
  showModal.value = false;
}

function pedirEliminar(e: Empleado): void {
  empleadoAEliminar.value = e;
  showDeleteModal.value = true;
}

function onEliminar(): void {
  if (empleadoAEliminar.value) {
    empleados.value = empleados.value.filter(
      (e: Empleado) => e.id !== empleadoAEliminar.value!.id,
    );
  }
  showDeleteModal.value = false;
  empleadoAEliminar.value = null;
}

function fmtDate(d: string): string {
  return new Date(d).toLocaleDateString("es-MX", {
    year: "numeric",
    month: "short",
    day: "2-digit",
  });
}
</script>

<template>
  <div
    style="
      position: fixed;
      inset: 0;
      display: flex;
      font-family: 'Jost', sans-serif;
      background: #f0e8dc;
      overflow: hidden;
    "
  >
    <!-- BG floral sutil -->
    <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;">
      <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;width:100%;height:100%" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
        <ellipse cx="1380" cy="820" rx="50" ry="120" fill="#C4A882" opacity="0.11" transform="rotate(15,1380,820)"/>
        <ellipse cx="1360" cy="40" rx="35" ry="85" fill="#D47878" opacity="0.10" transform="rotate(40,1360,40)"/>
        <ellipse cx="40" cy="880" rx="45" ry="110" fill="#D4A8B0" opacity="0.12" transform="rotate(-30,40,880)"/>
      </svg>
    </div>

    <!-- ── Sidebar ── -->
    <MenuLateral active-route="empleados" @navigate="emit('navigate', $event)" />

    <!-- ── Main ── -->
    <main style="flex:1;display:flex;flex-direction:column;padding:28px 32px;min-width:0;position:relative;z-index:5;overflow:hidden;">

      <!-- Topbar -->
      <header style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap;">
        <div style="text-align:left;">
          <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:30px;font-weight:700;color:#7a4e38;margin:0;line-height:1.1;">
            Gestión de Empleados
          </h2>
          <p style="font-size:12px;color:#a896a0;margin:3px 0 0;">
            {{ empleados.length }} empleados · {{ totalActivos }} activos
          </p>
        </div>

        <!-- Search + Nuevo -->
        <div style="display:flex;align-items:center;gap:10px;margin-left:auto;">
          <div style="display:flex;align-items:center;gap:8px;background:white;border:1.5px solid rgba(212,168,176,0.4);border-radius:50px;padding:8px 16px;box-shadow:0 2px 8px rgba(122,78,56,0.06);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#C4A882" stroke-width="2">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input v-model="busqueda" type="text" placeholder="Buscar empleado..."
              style="border:none;outline:none;background:transparent;font-size:13px;color:#7a4e38;font-family:'Jost',sans-serif;width:170px;"/>
          </div>
          <button @click="openCrear"
            style="display:flex;align-items:center;gap:7px;padding:10px 22px;border:none;border-radius:50px;cursor:pointer;font-size:12px;font-weight:600;letter-spacing:0.05em;color:white;font-family:'Jost',sans-serif;background:linear-gradient(135deg,#d4a8b0 0%,#c4a882 50%,#7a4e38 100%);box-shadow:0 6px 18px rgba(122,78,56,0.25);white-space:nowrap;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nuevo Empleado
          </button>
        </div>
      </header>

      <!-- Tabla -->
      <div style="flex:1;background:white;border-radius:20px;box-shadow:0 8px 40px rgba(122,78,56,0.1);overflow:auto;">

        <!-- Empty state -->
        <div v-if="empleadosFiltrados.length === 0"
          style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;color:#a896a0;">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#D4A8B0" stroke-width="1.2" style="margin-bottom:12px">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          <p style="font-size:14px;">No se encontraron empleados</p>
        </div>

        <table v-else style="width:100%;border-collapse:collapse;font-size:13px;">
          <colgroup>
            <col style="width:24%"/>
            <col style="width:22%"/>
            <col style="width:14%"/>
            <col style="width:14%"/>
            <col style="width:14%"/>
            <col style="width:12%"/>
          </colgroup>
          <thead>
            <tr style="background:linear-gradient(135deg,#d4a8b0 0%,#c4a882 55%,#9a7a5a 100%);">
              <th style="padding:14px 20px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Empleado</th>
              <th style="padding:14px 20px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Correo Electrónico</th>
              <th style="padding:14px 20px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Teléfono</th>
              <th style="padding:14px 20px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Cargo</th>
              <th style="padding:14px 20px;text-align:left;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Contratación</th>
              <th style="padding:14px 20px;text-align:center;color:white;font-size:10px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in empleadosFiltrados" :key="e.id"
              style="border-bottom:1px solid rgba(212,168,176,0.15);transition:background 0.15s;"
              @mouseenter="($event.currentTarget as HTMLElement).style.background='rgba(240,232,220,0.4)'"
              @mouseleave="($event.currentTarget as HTMLElement).style.background='transparent'">

              <!-- Empleado -->
              <td style="padding:16px 20px;vertical-align:middle;">
                <div style="display:flex;align-items:center;gap:12px;">
                  <div style="width:36px;height:36px;min-width:36px;border-radius:50%;background:linear-gradient(135deg,#d4a8b0,#c4a882);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;">
                    {{ e.nombre.charAt(0) }}
                  </div>
                  <div>
                    <span style="font-weight:600;color:#7a4e38;font-size:13px;display:block;">{{ e.nombre }}</span>
                    <!-- Badge estado -->
                    <span :style="`display:inline-flex;align-items:center;gap:4px;margin-top:3px;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:600;letter-spacing:0.05em;
                      ${e.estado === 'activo'
                        ? 'background:rgba(100,180,130,0.12);color:#3d8a5a;'
                        : 'background:rgba(180,100,100,0.1);color:#a04040;'}`">
                      <span :style="`width:5px;height:5px;border-radius:50%;background:${e.estado === 'activo' ? '#3d8a5a' : '#a04040'}`"></span>
                      {{ e.estado }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Correo -->
              <td style="padding:16px 20px;vertical-align:middle;color:#5a4040;font-size:13px;text-align:left;">{{ e.correo }}</td>

              <!-- Teléfono -->
              <td style="padding:16px 20px;vertical-align:middle;color:#5a4040;font-size:13px;text-align:left;">{{ e.telefono }}</td>

              <!-- Cargo -->
              <td style="padding:16px 20px;vertical-align:middle;text-align:left;">
                <span style="padding:4px 10px;border-radius:50px;background:rgba(196,168,130,0.15);color:#7a4e38;font-size:11px;font-weight:600;">
                  {{ e.cargo }}
                </span>
              </td>

              <!-- Fecha contratación -->
              <td style="padding:16px 20px;vertical-align:middle;color:#a896a0;font-size:13px;text-align:left;">
                {{ fmtDate(e.fechaContratacion) }}
              </td>

              <!-- Acciones -->
              <td style="padding:16px 20px;vertical-align:middle;">
                <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                  <!-- Editar -->
                  <button @click="openEditar(e)" title="Editar"
                    style="width:48px;height:48px;border-radius:10px;border:none;background:#c4a882;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;"
                    @mouseenter="($event.currentTarget as HTMLElement).style.background='#8B6B3D'"
                    @mouseleave="($event.currentTarget as HTMLElement).style.background='#C4A882'">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>

                  <!-- Eliminar -->
                  <button @click="pedirEliminar(e)" title="Eliminar"
                    style="width:48px;height:48px;border-radius:10px;border:none;background:#d4a8b0;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;"
                    @mouseenter="($event.currentTarget as HTMLElement).style.background='#B05070'"
                    @mouseleave="($event.currentTarget as HTMLElement).style.background='#D4A8B0'">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                      <path d="M10 11v6"/><path d="M14 11v6"/>
                      <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- ── Modales ── -->
    <ModalEmpleado
      :show="showModal"
      :action="modalAction"
      :empleado="selectedEmpleado"
      :show-delete="showDeleteModal"
      :empleado-a-eliminar="empleadoAEliminar"
      @close="showModal = false"
      @close-delete="showDeleteModal = false"
      @guardar="onGuardar"
      @eliminar="onEliminar"
    />
  </div>
</template>