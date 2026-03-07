<script setup lang="ts">
import { ref } from 'vue'
import Roles         from './components/Roles.vue'
import Clientes      from './components/Clientes.vue'
import Empleados     from './components/Empleados.vue'
import Categorias    from './components/Categorias.vue'
import Servicios     from './components/Servicios.vue'
import Reservaciones from './components/Reservaciones.vue'

const currentRoute = ref('roles')
</script>

<template>
  <Roles          v-if="currentRoute === 'roles'"          @navigate="currentRoute = $event" />
  <Clientes       v-if="currentRoute === 'clientes'"       @navigate="currentRoute = $event" />
  <Empleados      v-if="currentRoute === 'empleados'"      @navigate="currentRoute = $event" />
  <Categorias     v-if="currentRoute === 'categorias'"     @navigate="currentRoute = $event" />
  <Servicios      v-if="currentRoute === 'servicios'"      @navigate="currentRoute = $event" />
  <Reservaciones  v-if="currentRoute === 'reservaciones'"  @navigate="currentRoute = $event" />
</template>

<style>
html, body, #app { margin:0; padding:0; width:100%; height:100%; overflow:hidden; }
</style>