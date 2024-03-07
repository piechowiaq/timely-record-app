<script setup>

import {useForm, usePage} from '@inertiajs/vue3';
import {defineProps} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";


const props = defineProps({
    user: {
        type: Object,
    },
});


const form = useForm({});

const projectId = usePage().props.auth.user.project_id;

const sendRegistrationLink = () => {
    form.post(route('registration.send', props.user.data));
};

</script>

<template>
    <section class="space-y-6">

        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">User Registration</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                User not yet registered. Send link and advise user to check email for registration link.
            </p>
        </header>
        {{ user.data }}
        <form @submit.prevent="sendRegistrationLink">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Send Registration Link
            </PrimaryButton>
        </form>

    </section>
</template>
