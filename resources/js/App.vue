<script setup>
import { ref } from 'vue'
import Appbar from '@/components/Appbar.vue'
import Footer from '@/components/Footer.vue'
import TaskList from './components/TaskList.vue'
import TaskForm from './components/TaskForm.vue'

const editingTask = ref(null)

const setEditingTask = (task) => {
  editingTask.value = task
}

const handleSaved = () => {
  editingTask.value = null
}
</script>

<template>
  <div class="content-tasks">

    <!-- Passa a tarefa em edição + evento de salvamento -->
    <TaskForm :editingTask="editingTask" @saved="handleSaved" />

    <!-- Escuta o clique em "Editar" na tabela -->
    <TaskList @edit="setEditingTask" />

    <Footer />
  </div>
</template>

