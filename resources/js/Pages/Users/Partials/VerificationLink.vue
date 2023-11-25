<script setup>

import {usePage} from '@inertiajs/vue3';
import {ref} from 'vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const props = defineProps({
    user: {
        type: Object,
    },
});


const projectId = usePage().props.auth.user.project_id;


</script>

<template>
    <section class="space-y-6">

        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Verify Email</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Email not yet verified. Please advise user to check email for verification link.
            </p>
        </header>


        <PrimaryButton>Send Verification Link</PrimaryButton>


    </section>
</template>
