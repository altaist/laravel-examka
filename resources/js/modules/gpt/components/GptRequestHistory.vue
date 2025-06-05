<template>
  <div class="gpt-request-history">
    <div class="space-y-4">
      <div v-for="request in requests" :key="request.id" class="bg-white shadow rounded-lg p-4">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-lg font-medium text-gray-900">
              {{ request.metadata?.model || 'Неизвестная модель' }}
            </h3>
            <p class="text-sm text-gray-500">
              {{ new Date(request.created_at).toLocaleString() }}
            </p>
          </div>
          <span
            :class="{
              'bg-green-100 text-green-800': request.status === 'completed',
              'bg-yellow-100 text-yellow-800': request.status === 'processing',
              'bg-red-100 text-red-800': request.status === 'failed'
            }"
            class="px-2 py-1 text-xs font-medium rounded-full"
          >
            {{ getStatusText(request.status) }}
          </span>
        </div>

        <div class="mt-4 space-y-4">
          <div>
            <h4 class="text-sm font-medium text-gray-700">Промпт:</h4>
            <p class="mt-1 text-sm text-gray-900">{{ request.prompt }}</p>
          </div>

          <div v-if="request.response">
            <h4 class="text-sm font-medium text-gray-700">Ответ:</h4>
            <p class="mt-1 text-sm text-gray-900">{{ request.response }}</p>
          </div>

          <div v-if="request.error">
            <h4 class="text-sm font-medium text-red-700">Ошибка:</h4>
            <p class="mt-1 text-sm text-red-900">{{ request.error }}</p>
          </div>
        </div>
      </div>

      <div v-if="requests.length === 0" class="text-center py-8">
        <p class="text-gray-500">История запросов пуста</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useGptStore } from '../stores/gpt'

const props = defineProps({
  documentId: {
    type: Number,
    required: true
  }
})

const gptStore = useGptStore()

const requests = computed(() => {
  return gptStore.getRequestsForDocument(props.documentId)
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