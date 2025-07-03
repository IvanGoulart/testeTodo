import { defineStore } from 'pinia'
import axios from 'axios'
const api = import.meta.env.VITE_API_BASE_URL

export const useTaskStore = defineStore('task', {
  state: () => ({
    tasks: [],
  }),

  actions: {
    async fetchTasks() {
      const res = await axios.get('http://localhost:8001/api/tasks')
      this.tasks = res.data
    },

    async addTask(taskData) {
      await axios.post(`${api}/tasks`, taskData)
      await this.fetchTasks()
    },

    async updateTask(id, data) {
      await axios.put(`${api}/tasks/${id}`, data)
      await this.fetchTasks()
    },

    async toggleCompleted(task) {
        await axios.put(`${api}/tasks/${task.id}/toggle-finalizado`)
    },

    async softDeleteTask(task) {
        await axios.delete(`${api}/tasks/${task.id}`)
    }
  },

})
