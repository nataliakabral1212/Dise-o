<script setup lang="ts">
import { ref } from 'vue'
<<<<<<< Updated upstream
import { useRouter } from 'vue-router'
import MenuLateral from './components/MenuLateral.vue'
=======
import Roles from './components/Roles.vue'
import Clientes from './components/Clientes.vue'
>>>>>>> Stashed changes

const router = useRouter()
const activeRoute = ref('')

function navigate(route: string) {
  activeRoute.value = route
  router.push(route)
}
</script>

<template>
<<<<<<< Updated upstream
  <div style="display:flex;width:100%;height:100%;">
    <MenuLateral :activeRoute="activeRoute" @navigate="navigate" />
    <div style="flex:1;overflow:auto;">
      <RouterView />
    </div>
  </div>
</template>

<style>
html, body, #app { margin:0; padding:0; width:100%; height:100%; overflow:hidden; }
=======
  <Roles v-if="currentRoute === 'roles'" @navigate="currentRoute = $event" />
  <Clientes v-if="currentRoute === 'clientes'" @navigate="currentRoute = $event" />
  <Categorias v-if="currentRoute === 'categorias'" @navigate="currentRoute = $event" />
  <Servicios v-if="currentRoute === 'servicios'" @navigate="currentRoute = $event" />
</template>

<style>
html,
body,
#app {
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
}
>>>>>>> Stashed changes
</style>
