<template>
  <div class="px-6"> <!-- 👈 Margem lateral aplicada aqui -->
    <div class="overflow-x-auto rounded-lg shadow bg-white">
      <table class="min-w-full text-sm text-gray-700 table-auto">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wide">
          <tr>
            <th class="px-4 py-3 text-center w-10">✓</th>
            <th class="px-4 py-3 text-left">Nome</th>
            <th class="px-4 py-3 text-left">Descrição</th>
            <th class="px-4 py-3 text-center w-32">Data Limite</th>
            <th class="px-4 py-3 text-center w-40">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="task in tasks"
            :key="task.id"
            class="border-t hover:bg-gray-50 transition"
          >
            <td class="px-4 py-3 text-center">
          <input
  type="checkbox"
  :checked="checkboxStatus[task.id]"
  :disabled="checkboxStatus[task.id]"
  @change="(e) => onCheckboxChange(task, e)"
  class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
/>
            </td>

            <td class="px-4 py-3 font-medium">
              <span
                :class="{
                  'line-through text-gray-400': task.finalizado,
                  'text-gray-800': !task.finalizado
                }"
              >
                {{ task.nome }}
              </span>
            </td>

            <td class="px-4 py-3 text-gray-600">
              {{ task.descricao }}
            </td>

            <td class="px-4 py-3 text-center text-gray-600">
              {{ formatarData(task.data_limite) }}
            </td>

            <td class="px-4 py-3 text-center space-x-2">
              <button
                class="inline-flex items-center px-3 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded hover:bg-indigo-100 hover:text-indigo-700 transition"
                @click="$emit('edit', task)"
              >
                Editar
              </button>
              <button
                class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 hover:text-red-700 transition"
                @click="deletar(task)"
              >
                Deletar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useTaskStore } from '@/stores/task'

const taskStore = useTaskStore()
const tasks = computed(() => taskStore.tasks)

const checkboxStatus = ref({}) // Armazena status individual dos checkboxes
const deletandoIds = ref(new Set())
onMounted(() => {
  taskStore.fetchTasks()
})

// Atualiza checkboxStatus quando tarefas forem carregadas
// Atualiza o status sempre que as tarefas mudarem

const onCheckboxChange = async (task, event) => {
  const checked = event.target.checked;

  if (!checked) {
    // Se tentou desmarcar, impede e volta visualmente
    checkboxStatus.value[task.id] = true;
    return;
  }

  // Marca e desativa o checkbox imediatamente
  checkboxStatus.value[task.id] = true;

  await taskStore.toggleCompleted(task);
  await taskStore.fetchTasks();
};

// Função para formatar data (yyyy-mm-dd → dd/mm/yyyy)
const formatarData = (data) => {
  if (!data) return ''
  // Garante que a data seja tratada como local (sem conversão para UTC)
  const [year, month, day] = data.split('-').map(Number)
  const d = new Date(year, month - 1, day) // month é 0-based no JavaScript
  return d.toLocaleDateString('pt-BR', { timeZone: 'America/Sao_Paulo' })
}

const deletar = async (task) => {
  if (!confirm(`Tem certeza que deseja excluir a tarefa "${task.nome}"?`)) {
    return
  }

  deletandoIds.value.add(task.id)

  try {
    await taskStore.softDeleteTask(task)
    await taskStore.fetchTasks()
  } finally {
    deletandoIds.value.delete(task.id)
  }
}
</script>
<style scoped>
/* Adicione estilos personalizados se necessário */
</style>

