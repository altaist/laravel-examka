<template>
  <Head title="GPT Запросы" />
     

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          GPT Запросы
        </h2>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <SimpleGptPage :requests="requests" />
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/_breeze/Layouts/AuthenticatedLayout.vue'
import SimpleGptPage from '@/modules/gpt/components/SimpleGptPage.vue'

defineProps({
  requests: {
    type: Array,
    required: true
  }
})
</script> 