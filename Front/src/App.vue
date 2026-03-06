<script setup lang="ts">
import { ref } from 'vue'
import Categorias from './components/Categorias.vue'

const currentRoute = ref('categorias')
</script>

<template>
  <Categorias @navigate="currentRoute = $event" />
</template>

<style>
html, body, #app { margin:0; padding:0; width:100%; height:100%; overflow:hidden; }
</style>