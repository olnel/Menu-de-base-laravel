<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed, onMounted } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import usePermissions from "@/UserPermissions/usePermissions.js";
import FormItem from "@/Components/FormItem.vue";
import BaseUploadImage from "@/Components/UploadImage.vue";
import { message } from "ant-design-vue";

const { can } = usePermissions();

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
});

function download(model) {
    const url = `/modelExcel/${model}.xlsx`;
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `Modèle_${model}.xlsx`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

</script>
<template>
  <AuthenticatedLayout :title="title" selected-menu="modeledocument">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ title }}
      </h2>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8 bg-white shadow sm:rounded-lg h-full">
      <h1 class="text-2xl font-bold mb-4">Modèle de Document</h1>
      <p class="mb-4">
        Téléchargez le modèle de document pour l'importation de données.
      </p>
      <br>
      <br>

      <div class="flex justify-center items-center mt-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
          <a-card
            v-for="model in data"
            :key="model"
            hoverable
            class="w-64 h-64 cursor-pointer"
            @click="download(model)"
          >
            <div class="flex flex-col items-center justify-center p-4 h-full">
              <svg
                class="text-green-600 mb-6 flex-shrink-0"
                style="width: 100px; height: 100px"
                aria-hidden="true"
                focusable="false"
                data-prefix="fas"
                data-icon="file-excel"
                role="img"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 384 512"
              >
                <path
                  fill="currentColor"
                  d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM155.7 250.2L192 302.1l36.3-51.9c7.6-10.9 22.6-13.5 33.4-5.9s13.5 22.6 5.9 33.4L221.3 344l46.4 66.2c7.6 10.9 5 25.8-5.9 33.4s-25.8 5-33.4-5.9L192 385.8l-36.3 51.9c-7.6 10.9-22.6 13.5-33.4 5.9s-13.5-22.6-5.9-33.4L162.7 344l-46.4-66.2c-7.6-10.9-5-25.8 5.9-33.4s25.8-5 33.4 5.9z"
                ></path>
              </svg>
            </div>

            <a-card-meta>
              <template #title>
                <div class="text-lg font-semibold text-center w-full">
                  Modèle {{ model }}
                </div>
              </template>
            </a-card-meta>
          </a-card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

