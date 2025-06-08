<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-toolbar-title>
          {{ title }}
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <slot></slot>
    </q-page-container>

    <q-footer elevated class="bg-grey-8 text-white">
      <q-toolbar>
        <q-toolbar-title>
          <div class="text-caption">© 2024 Все права защищены</div>
        </q-toolbar-title>
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Сервис написания курсовых работ'
  }
})
</script> 