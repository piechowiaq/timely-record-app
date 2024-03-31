<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import {Head, usePage} from '@inertiajs/vue3';
import UpdateProjectInformationForm from "@/Pages/Profile/Partials/UpdateProjectInformationForm.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    canManageProject: {
        type: Boolean,
    },
});

const isSuperAdmin = usePage().props.auth.user.roles.map(role => role.name).includes('super-admin');

</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Profile</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div v-if="canManageProject && !isSuperAdmin" class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow ">
                    <UpdateProjectInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow ">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow ">
                    <UpdatePasswordForm class="max-w-xl"/>
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow ">
                    <DeleteUserForm class="max-w-xl"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
