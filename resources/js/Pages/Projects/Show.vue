<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
    workspaces: {
        type: Object,
    },
});

const projectId = usePage().props.projectId;
</script>

<template>
    <Head title="Project" />

    <AuthenticatedLayout>
        <template #header>
            <h2>Project Settings</h2>
        </template>

        <div class="px-2 pb-2">
            <div class="space-y-2 dark:bg-gray-700 dark:text-gray-400">
                <div class="bg-white p-4 shadow dark:bg-gray-800 sm:p-8">
                    <section>
                        <header>
                            <h2
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                Projects Information
                            </h2>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Create your project's workspace information.
                            </p>
                        </header>
                        <ul>
                            <li v-if="!workspaces.length"></li>
                            <li
                                v-for="workspace in workspaces"
                                :key="workspace.id"
                            >
                                <Link
                                    :href="
                                        route('workspaces.edit', {
                                            project: projectId,
                                            workspace: workspace.id,
                                        })
                                    "
                                >
                                    {{ workspace.name }}
                                </Link>
                            </li>
                        </ul>

                        <Link
                            :href="route('workspaces.create', projectId)"
                            class="text-sm text-cyan-600 hover:text-cyan-700"
                        >
                            Create Workspace
                        </Link>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
