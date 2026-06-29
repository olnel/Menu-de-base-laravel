<script setup>
import { reactive } from 'vue';
import ButtonAnimation from "@/Pages/Public/Components/Button/ButtonAnimation.vue";


const emit = defineEmits(['message-sent']);

const form = reactive({
    lastName: '',
    email: '',
    country: '',
    phone: '',
    subject: '',
    message: ''
});

const formErrors = reactive({
    lastName: '',
    email: '',
    country: '',
    phone: '',
    subject: '',
    message: ''
});

const formFields = [
    { label: 'Nom', model: 'lastName', placeholder: 'Your name', required: true, col: 12, icon: 'user' },
    { label: 'Adresse e-mail', model: 'email', placeholder: 'Your e-mail address', required: true, col: 12, icon: 'envelope' },
    { label: 'Pays', model: 'country', placeholder: 'Pays', required: true, col: 12, icon: 'globe' },
    { label: 'Téléphone', model: 'phone', placeholder: 'Téléphone', required: false, col: 12, icon: 'phone' },
    { label: 'Objet', model: 'subject', placeholder: 'Objet', required: false, col: 24, icon: 'info-circle' },
    {
        label: 'Message',
        model: 'message',
        placeholder: 'Write a message here...',
        type: 'textarea',
        required: true,
        col: 24,
        rows: 6,
        maxlength: 100,
        showCount: true,
        icon: 'comment'
    }
];

const validateForm = () => {
    let isValid = true;

    // Reset errors
    Object.keys(formErrors).forEach(key => formErrors[key] = '');

    // Check required fields
    formFields.forEach(field => {
        if (field.required && !form[field.model]) {
            formErrors[field.model] = `Le champ ${field.label} est obligatoire`;
            isValid = false;
        }
    });

    // Validate email
    if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        formErrors.email = 'Adresse e-mail invalide';
        isValid = false;
    }

    return isValid;
};

const sendMessage = () => {
    if (validateForm()) {
        console.log('Form data:', form);
        alert('Message envoyé avec succès!');

        // Reset form
        Object.keys(form).forEach(key => form[key] = '');

        // Emit event
        emit('message-sent');
    } else {
        alert('Veuillez remplir tous les champs obligatoires');
    }
};
</script>

<template>
    <div class="p-4 md:p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Envoyez-nous un Message</h2>

        <form class="space-y-4" @submit.prevent="sendMessage">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="(field, index) in formFields" :key="index"
                     :class="{'col-span-1': field.col === 12, 'col-span-2': field.col === 24}">
                    <div class="mb-4">
                        <label :for="field.model" class="block text-sm font-semibold text-gray-700 mb-1">
                            {{ field.label }} <span v-if="field.required" class="text-red-600">*</span>
                        </label>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <font-awesome-icon :icon="field.icon" class="text-gray-400" />
                            </div>

                            <input v-if="field.type !== 'textarea'"
                                   :type="field.type || 'text'"
                                   :id="field.model"
                                   v-model="form[field.model]"
                                   :placeholder="field.placeholder"
                                   class="block w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   :class="{'border-red-500': formErrors[field.model]}"
                            />

                            <textarea v-else
                                      :id="field.model"
                                      v-model="form[field.model]"
                                      :placeholder="field.placeholder"
                                      :rows="field.rows || 4"
                                      :maxlength="field.maxlength"
                                      class="block w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      :class="{'border-red-500': formErrors[field.model]}"
                            ></textarea>

                            <div v-if="field.showCount && field.type === 'textarea'" class="text-right text-xs text-gray-500 mt-1">
                                {{ form[field.model].length }}/{{ field.maxlength }}
                            </div>
                        </div>

                        <p v-if="formErrors[field.model]" class="mt-1 text-sm text-red-600">
                            {{ formErrors[field.model] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <ButtonAnimation
                    label="Envoyer le message"
                    icon="paper-plane"
                    backgroundColor="bg-green-600"
                    hoverColor="hover:bg-green-700"
                    borderColor="border-green-600"
                    textColor="text-white"
                    class="flex items-center justify-center font-medium w-full"
                    @click="sendMessage"
                />
            </div>
        </form>
    </div>
</template>
