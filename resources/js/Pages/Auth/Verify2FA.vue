<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import FormItem from "@/Components/FormItem.vue";
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { message } from 'ant-design-vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    code: '',
});

const digits = ref(['', '', '', '', '', '']);
const inputRefs = ref([]);
const resending = ref(false);

const handleInput = (e, index) => {
    const val = e.target.value;
    
    // Only allow numeric digits
    if (!/^\d*$/.test(val)) {
        digits.value[index] = '';
        form.code = digits.value.join('');
        return;
    }
    
    if (val.length > 0) {
        digits.value[index] = val.charAt(val.length - 1);
        if (index < 5) {
            inputRefs.value[index + 1]?.focus();
        }
    }
    form.code = digits.value.join('');
};

const handleKeyDown = (e, index) => {
    if (e.key === 'Backspace') {
        if (!digits.value[index] && index > 0) {
            digits.value[index - 1] = '';
            inputRefs.value[index - 1]?.focus();
        } else {
            digits.value[index] = '';
        }
        form.code = digits.value.join('');
    } else if (e.key === 'ArrowLeft' && index > 0) {
        inputRefs.value[index - 1]?.focus();
    } else if (e.key === 'ArrowRight' && index < 5) {
        inputRefs.value[index + 1]?.focus();
    }
};

const handlePaste = (e) => {
    e.preventDefault();
    const clipboardData = e.clipboardData || window.clipboardData;
    const pastedData = clipboardData.getData('Text');
    const numericData = pastedData.replace(/\D/g, '').slice(0, 6);
    
    for (let i = 0; i < 6; i++) {
        digits.value[i] = numericData[i] || '';
    }
    
    form.code = digits.value.join('');
    
    // Focus last character or next empty one
    const focusIndex = Math.min(numericData.length, 5);
    inputRefs.value[focusIndex]?.focus();
};

const submit = () => {
    if (!form.code || form.code.length !== 6) {
        form.setError('code', 'Veuillez saisir le code à 6 chiffres.');
        return;
    }
    form.clearErrors();
    form.post(route('login.2fa.verify'), {
        onFinish: () => {
            // Keep the entered code on error or clear it depending on preference.
            // Let's clear on complete failure
            if (form.errors.code) {
                digits.value = ['', '', '', '', '', ''];
                form.code = '';
                inputRefs.value[0]?.focus();
            }
        },
    });
};

const resendCode = async () => {
    resending.value = true;
    try {
        await axios.post(route('login.2fa.resend'));
        message.success('Un nouveau code de vérification a été envoyé par e-mail.');
        // Reset code inputs
        digits.value = ['', '', '', '', '', ''];
        form.code = '';
        inputRefs.value[0]?.focus();
    } catch (error) {
        message.error('Impossible de renvoyer le code. Veuillez réessayer.');
    } finally {
        resending.value = false;
    }
};

onMounted(() => {
    // Focus the first input automatically
    inputRefs.value[0]?.focus();
});
</script>

<template>
    <GuestLayout>
        <Head title="Vérification 2FA" />

        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-gray-900">Vérification de sécurité</h2>
            <p class="text-sm text-gray-500 mt-2">
                Un code de vérification à 6 chiffres a été envoyé à votre adresse e-mail. Veuillez le saisir ci-dessous pour finaliser votre connexion.
            </p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
            {{ status }}
        </div>

        <a-form @submit.prevent="submit" layout="vertical" class="space-y-6">
            <form-item :help="form.errors.code">
                <div class="flex justify-center gap-3 my-4">
                    <input
                        v-for="(digit, index) in digits"
                        :key="index"
                        :ref="el => { if (el) inputRefs[index] = el }"
                        v-model="digits[index]"
                        type="text"
                        maxlength="1"
                        class="w-12 h-12 text-center text-xl font-bold border rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all shadow-sm"
                        style="border-color: #d1d5db;"
                        @input="handleInput($event, index)"
                        @keydown="handleKeyDown($event, index)"
                        @paste="handlePaste($event)"
                    />
                </div>
            </form-item>

            <a-button type="primary" size="large" html-type="submit" class="w-full" :loading="form.processing">
                Confirmer la connexion
            </a-button>

            <div class="flex justify-between items-center text-sm pt-2">
                <Link :href="route('login')" class="text-gray-600 hover:text-gray-900 underline">
                    Retour à la connexion
                </Link>
                
                <a-button type="link" :loading="resending" @click="resendCode" class="!p-0 h-auto">
                    Renvoyer le code
                </a-button>
            </div>
        </a-form>
    </GuestLayout>
</template>

<style scoped>
:deep(.ant-btn-primary) {
    border-radius: 8px;
}
</style>
