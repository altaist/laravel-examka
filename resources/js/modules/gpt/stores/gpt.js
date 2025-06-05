import { defineStore } from 'pinia'
import axios from 'axios'

export const useGptStore = defineStore('gpt', {
  state: () => ({
    requests: [],
    availableServices: ['openai', 'anthropic'],
    models: {
      openai: {
        'gpt-3.5-turbo': 'GPT-3.5 Turbo',
        'gpt-4': 'GPT-4'
      },
      anthropic: {
        'claude-2': 'Claude 2',
        'claude-instant': 'Claude Instant'
      }
    }
  }),

  getters: {
    getModelsForService: (state) => (service) => {
      return state.models[service] || {}
    },

    getRequestsForDocument: (state) => (documentId) => {
      return state.requests
        .filter(request => request.document_id === documentId)
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    }
  },

  actions: {
    async createRequest(requestData) {
      try {
        const response = await axios.post('/api/gpt-requests', requestData)
        this.requests.unshift(response.data)
        return response.data
      } catch (error) {
        console.error('Failed to create GPT request:', error)
        throw error
      }
    },

    async fetchRequests(documentId) {
      try {
        const response = await axios.get(`/api/documents/${documentId}/gpt-requests`)
        this.requests = response.data
      } catch (error) {
        console.error('Failed to fetch GPT requests:', error)
        throw error
      }
    },

    async updateRequestStatus(requestId, status, response = null, error = null) {
      const request = this.requests.find(r => r.id === requestId)
      if (request) {
        request.status = status
        if (response) request.response = response
        if (error) request.error = error
      }
    }
  }
}) 