<template>
  <div class="gpt-module">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div>
        <h2 class="text-lg font-medium text-gray-900 mb-4">Новый запрос</h2>
        <GptRequestForm
          :document-id="documentId"
          @request-sent="handleRequestSent"
        />
      </div>

      <div>
        <h2 class="text-lg font-medium text-gray-900 mb-4">История запросов</h2>
        <GptRequestHistory :document-id="documentId" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useGptStore } from '../stores/gpt'
import GptRequestForm from './GptRequestForm.vue'
import GptRequestHistory from './GptRequestHistory.vue'

const props = defineProps({
  documentId: {
    type: Number,
    required: true
  }
})

const gptStore = useGptStore()

onMounted(async () => {
  await gptStore.fetchRequests(props.documentId)
})

const handleRequestSent = (request) => {
  // Можно добавить дополнительную логику после отправки запроса
  console.log('Request sent:', request)
}
</script> 