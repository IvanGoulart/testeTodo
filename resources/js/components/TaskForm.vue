<template>
  <form @submit.prevent="handleSubmit" class="bg-white p-4 rounded shadow mb-6 space-y-4">
    <h2 class="text-xl font-bold">{{ isEditing ? '✏️ Editar Tarefa' : '➕ Nova Tarefa' }}</h2>

    <!-- Campo Nome -->
    <div>
      <label class="block text-sm font-medium mb-1">Nome</label>
      <input
        v-model="form.nome"
        type="text"
        placeholder="Digite o nome"
        class="w-full border border-gray-300 rounded px-3 py-2"
        required
      />
    </div>
    <!-- Campo Descrição -->
    <div>
      <label class="block text-sm font-medium mb-1">Descrição</label>
      <textarea
        v-model="form.descricao"
        placeholder="Digite a descrição"
        class="w-full border border-gray-300 rounded px-3 py-2"
        rows="3"
      ></textarea>
    </div>

    <!-- Campo Data Limite -->
    <div>
      <label class="block text-sm font-medium mb-1">Data Limite</label>
      <input
        v-model="form.data_limite"
        type="date"
        class="w-full border border-gray-300 rounded px-3 py-2"
        lang="pt-BR"
      />
    </div>

    <!-- Botão -->
    <button
      type="submit"
      class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition"
    >
      <svg
        v-if="!isEditing"
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m4-4H8" />
      </svg>
      {{ isEditing ? 'Salvar Alterações' : 'Salvar Tarefa' }}
    </button>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useTaskStore } from '../stores/task'
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  editingTask: Object
})

const emit = defineEmits(['saved'])

const taskStore = useTaskStore()

const form = ref({
  nome: '',
  descricao: '',
  data_limite: '',
  finalizado: false
})

const isEditing = ref(false)

const resetForm = () => {
  form.value = {
    nome: '',
    descricao: '',
    data_limite: '',
    finalizado: false
  }
  isEditing.value = false
}

// Atualiza o formulário ao receber uma tarefa para edição
watch(() => props.editingTask, (newTask) => {
  if (newTask) {
    const formattedDate = formatarDataInput(newTask.data_limite)
    form.value = {
      ...newTask,
      data_limite: formattedDate
    }
    isEditing.value = true
  } else {
    resetForm()
  }
}, { immediate: true })

const handleSubmit = async () => {
  const taskData = {
    ...form.value,
    data_limite: form.value.data_limite ? form.value.data_limite : ''
  }

  if (isEditing.value) {
    await taskStore.updateTask(form.value.id, taskData)
  } else {
    await taskStore.addTask(taskData)
  }

  emit('saved')
  resetForm()
}

const formatarDataInput = (data) => {
  if (!data) return ''
  // Trata a data como local (YYYY-MM-DD) sem conversão para UTC
  let dateStr = data
  if (typeof data !== 'string') {
    dateStr = new Date(data).toISOString().split('T')[0]
  }
  if (dateStr.includes('T')) {
    dateStr = dateStr.split('T')[0]
  }
  const [year, month, day] = dateStr.split('-').map(Number)
  const formatted = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`
  return formatted
}
</script>
