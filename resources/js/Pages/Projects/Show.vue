<script setup>
import { Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    workspaces: {
        type: Object,
    }
});

const projectId = usePage().props.auth.user.project_id;

</script>

<template>
    <Head title="Project"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-white dark:text-gray-700 leading-tight">Project Settings</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow ">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Projects Information</h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Create your project's workspace information.
                            </p>
                        </header>
                        <ul>
                            <li v-for="workspace in workspaces" :key="workspace.id">

                                {{ workspace.id }} {{ workspace.name }}
                            </li>
                        </ul>

                        <Link :href="route('workspaces.create', projectId )" class="text-cyan-600 hover:text-cyan-700 text-sm">
                            Create Workspace
                        </Link>


                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

