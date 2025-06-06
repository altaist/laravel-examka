<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Форма отправки запроса -->
    <div class="mb-8">
      <form @submit.prevent="submitRequest" class="space-y-4">
        <div>
          <label for="prompt" class="block text-sm font-medium text-gray-700">Введите ваш запрос:</label>
          <textarea
            id="prompt"
            v-model="form.prompt"
            rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Введите ваш запрос..."
          ></textarea>
        </div>
        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
          >
            {{ form.processing ? 'Отправка...' : 'Отправить запрос' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Список запросов -->
    <div class="space-y-4">
      <div class="flex justify-between items-center">
        <h2 class="text-lg font-medium text-gray-900">История запросов</h2>
        <button
          @click="refresh"
          class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Обновить список
        </button>
      </div>

      <div v-for="request in requests" :key="request.id" class="bg-white shadow rounded-lg p-4">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm text-gray-500">
              Отправлено: {{ new Date(request.created_at).toLocaleString() }}
            </p>
            <p v-if="request.updated_at" class="text-sm text-gray-500">
              Получен ответ: {{ new Date(request.updated_at).toLocaleString() }}
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

        <div class="mt-4">
          <p class="text-sm font-medium text-gray-700">Запрос:</p>
          <p class="mt-1 text-sm text-gray-900">{{ request.prompt }}</p>
        </div>

        <div class="mt-4 flex justify-end">
          <button
            v-if="request.response"
            @click="openResponseModal(request)"
            class="text-sm text-indigo-600 hover:text-indigo-900"
          >
            Показать ответ
          </button>
        </div>
      </div>

      <div v-if="requests.length === 0" class="text-center py-8">
        <p class="text-gray-500">История запросов пуста</p>
      </div>
    </div>

    <!-- Модальное окно с ответом -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <div class="flex justify-between items-start">
          <h3 class="text-lg font-medium text-gray-900">Ответ сервиса</h3>
          <button
            @click="showModal = false"
            class="text-gray-400 hover:text-gray-500"
          >
            <span class="sr-only">Закрыть</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="mt-4">
          <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ selectedRequest?.response }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  requests: {
    type: Array,
    required: true
  }
})

const form = useForm({
  prompt: ''
})

const showModal = ref(false)
const selectedRequest = ref(null)

const submitRequest = () => {
  form.post(route('gpt.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}

const refresh = () => {
  router.visit(route('gpt.requests'))
}

const openResponseModal = (request) => {
  selectedRequest.value = request
  showModal.value = true
}

const getStatusText = (status) => {
  const statusMap = {
    completed: 'Завершено',
    processing: 'В обработке',
    failed: 'Ошибка'
  }
  return statusMap[status] || status
}
</script> 