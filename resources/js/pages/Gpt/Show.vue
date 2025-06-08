<template>
  <Head :title="`Запрос #${request.id}`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Детали запроса #{{ request.id }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="space-y-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Статус</h3>
                <p class="mt-1">
                  <span
                    :class="{
                      'bg-green-100 text-green-800': request.status === 'completed',
                      'bg-yellow-100 text-yellow-800': request.status === 'processing',
                      'bg-red-100 text-red-800': request.status === 'failed'
                    }"
                    class="px-2 py-1 text-sm font-medium rounded-full"
                  >
                    {{ getStatusText(request.status) }}
                  </span>
                </p>
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900">Запрос</h3>
                <p class="mt-1 text-gray-900">{{ request.prompt }}</p>
              </div>

              <div v-if="request.response">
                <h3 class="text-lg font-medium text-gray-900">Ответ</h3>
                <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ request.response }}</p>
              </div>

              <div v-if="request.error">
                <h3 class="text-lg font-medium text-red-900">Ошибка</h3>
                <p class="mt-1 text-red-900">{{ request.error }}</p>
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900">Временные метки</h3>
                <p class="mt-1 text-gray-900">
                  Создан: {{ new Date(request.created_at).toLocaleString() }}
                </p>
                <p v-if="request.updated_at" class="mt-1 text-gray-900">
                  Обновлен: {{ new Date(request.updated_at).toLocaleString() }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
  request: {
    type: Object,
    required: true
  }
})

const getStatusText = (status) => {
  const statusMap = {
    completed: 'Завершено',
    processing: 'В обработке',
    failed: 'Ошибка'
  }
  return statusMap[status] || status
}
</script> 