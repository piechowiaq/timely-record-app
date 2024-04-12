<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head, usePage } from "@inertiajs/vue3";
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

const isSuperAdmin = usePage()
    .props.auth.user.roles.map((role) => role.name)
    .includes("super-admin");
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Profile</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div
                    v-if="canManageProject && !isSuperAdmin"
                    class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8"
                >
                    <UpdateProjectInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
