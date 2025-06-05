<template>
  <div class="gpt-request-form">
    <form @submit.prevent="submitRequest" class="space-y-4">
      <div>
        <label for="service" class="block text-sm font-medium text-gray-700">Сервис</label>
        <select
          id="service"
          v-model="form.service"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option v-for="service in availableServices" :key="service" :value="service">
            {{ service }}
          </option>
        </select>
      </div>

      <div>
        <label for="model" class="block text-sm font-medium text-gray-700">Модель</label>
        <select
          id="model"
          v-model="form.model"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
          <option v-for="(name, id) in availableModels" :key="id" :value="id">
            {{ name }}
          </option>
        </select>
      </div>

      <div>
        <label for="temperature" class="block text-sm font-medium text-gray-700">
          Температура ({{ form.temperature }})
        </label>
        <input
          type="range"
          id="temperature"
          v-model.number="form.temperature"
          min="0"
          max="1"
          step="0.1"
          class="mt-1 block w-full"
        />
      </div>

      <div>
        <label for="prompt" class="block text-sm font-medium text-gray-700">Промпт</label>
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
          :disabled="isSubmitting"
          class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
        >
          {{ isSubmitting ? 'Отправка...' : 'Отправить запрос' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useGptStore } from '../stores/gpt'

const props = defineProps({
  documentId: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['request-sent'])

const gptStore = useGptStore()
const isSubmitting = ref(false)

const form = ref({
  service: 'openai',
  model: 'gpt-3.5-turbo',
  temperature: 0.7,
  prompt: ''
})

const availableServices = computed(() => gptStore.availableServices)
const availableModels = computed(() => gptStore.getModelsForService(form.value.service))

// Обновляем модель при смене сервиса
watch(() => form.value.service, (newService) => {
  const models = gptStore.getModelsForService(newService)
  form.value.model = Object.keys(models)[0]
})

const submitRequest = async () => {
  if (!form.value.prompt.trim()) return

  isSubmitting.value = true
  try {
    const response = await gptStore.createRequest({
      document_id: props.documentId,
      prompt: form.value.prompt,
      metadata: {
        service: form.value.service,
        model: form.value.model,
        temperature: form.value.temperature
      }
    })

    emit('request-sent', response)
    form.value.prompt = ''
  } catch (error) {
    console.error('Failed to send request:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script> 