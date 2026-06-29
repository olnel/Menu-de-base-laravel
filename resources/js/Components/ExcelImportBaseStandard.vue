<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { message } from 'ant-design-vue'

const props = defineProps({
  model: {
    type: String,
    required: true,
  },
  columns: {
    type: Array,
    required: true,
  },
  buttonText: {
    type: String,
    default: 'Importer Excel',
  },
  routeName: {
    type: String,
    default: 'import.excel',
  },
})

const file = ref(null)
const isLoading = ref(false)
const fileInput = ref(null)

const handleFileChange = (event) => {
  file.value = event.target.files[0]
  console.log('Fichier sélectionné:', file.value)
  submit()
}

const submit = () => {
  if (!file.value) return alert('Veuillez choisir un fichier Excel')

  const formData = new FormData()
  formData.append('file', file.value)
  formData.append('model', props.model)
  props.columns.forEach((col, index) => formData.append(`columns[${index}]`, col))

  isLoading.value = true

  router.post(route(props.routeName), formData, {
    forceFormData: true,
    onFinish: () => {
      isLoading.value = false
      file.value = null
      if (fileInput.value) {
        fileInput.value.value = ''
      }
    },
    // onSuccess: () => {
    //   message.success('Import réussi !')
    // },
    // onError: (errors) => {
    //   message.error('Erreur lors de l\'import')
    // },
  })
}
</script>

<template>
  <div class="flex items-center gap-2">
    <label
      class="inline-flex items-center text-base rounded cursor-pointer transition"
    >
    <font-awesome-icon class="text-[15px] mr-2" icon="file-excel"/>
      {{ buttonText }}
      <input
        type="file"
        ref="fileInput"
        class="hidden"
        accept=".xlsx,.xls,.csv"
        @change="handleFileChange"
      />
    </label>

    <!-- <button
      @click="submit"
      :disabled="isLoading || !file"
      class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded disabled:opacity-50"
    >
      {{ isLoading ? 'Importation...' : 'Valider' }}
    </button> -->
  </div>
</template>
