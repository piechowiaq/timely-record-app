<script setup>
import { Link, usePage } from "@inertiajs/vue3";

defineProps({
    workspace: {
        type: Object,
    },
    mostOutdatedRegistries: {
        type: Array,
    },
    expiringSoonRegistries: {
        type: Array,
    },
    recentlyUpdatedRegistries: {
        type: Array,
    },
    countOfUpToDateRegistries: {
        type: Number,
    },
    countOfExpiredRegistries: {
        type: Number,
    },
    percentageOfUpToDate: {
        type: Number,
    },
});

const projectId = usePage().props.auth.user.project_id;
</script>

<template>
    <section
        :class="{
            'border-green-600': percentageOfUpToDate === 100,
            'border-cyan-600': percentageOfUpToDate !== 100,
        }"
        class="flex-grow border bg-white p-4 text-gray-600"
    >
        <article class="font-bold">
            <header
                class="block flex items-center justify-between whitespace-nowrap border-b pb-2"
            >
                <h2 class="truncate">Trainings</h2>
                <template v-if="percentageOfUpToDate === 100">
                    <span
                        class="mb-2 rounded bg-green-500 px-2 text-xs font-medium text-white"
                        >EXCELLENT</span
                    >
                </template>
                <template v-else-if="percentageOfUpToDate > 90">
                    <span
                        class="mb-2 rounded bg-green-300 px-2 text-xs font-medium text-white"
                        >GOOD</span
                    >
                </template>
                <template v-else-if="percentageOfUpToDate > 80">
                    <span
                        class="mb-2 rounded bg-yellow-300 px-2 text-xs font-medium text-white"
                        >AVERAGE</span
                    >
                </template>
                <template v-else>
                    <span
                        class="mb-2 truncate rounded bg-red-500 px-2 text-xs font-medium text-white"
                        >NEEDS IMPROVEMENT</span
                    >
                </template>
            </header>

            <div class="flex justify-between py-2">
                <p class="text-5xl">
                    {{ percentageOfUpToDate }}%
                    <span class="truncate text-xs">COMPLETED</span>
                </p>
                <div>
                    <p class="text-sm">
                        <span class="truncate text-green-600"
                            >{{ countOfUpToDateRegistries }} valid</span
                        >
                    </p>
                    <p class="text-sm">
                        <span class="truncate text-red-600"
                            >{{ countOfExpiredRegistries }} expired</span
                        >
                    </p>
                </div>
            </div>
        </article>

        <aside class="py-2 text-xs">
            <h3 class="py-2 font-bold">Most Outdated:</h3>
            <ul
                v-if="mostOutdatedRegistries && mostOutdatedRegistries.length"
                class="text-cyan-600"
            >
                <li
                    v-for="registry in mostOutdatedRegistries"
                    :key="registry.name"
                    class="truncate py-1"
                >
                    <Link
                        :href="
                            route('workspaces.registries.show', {
                                project: projectId,
                                workspace: workspace,
                                registry: registry.registry_id,
                            })
                        "
                    >
                        {{ registry.name }}
                    </Link>
                </li>
            </ul>
            <p v-else class="italic text-green-600">
                All of the registries are updated.
            </p>
        </aside>
        <aside class="py-2 text-xs">
            <h3 class="py-2 font-bold">Expiring Soon:</h3>
            <ul
                v-if="expiringSoonRegistries && expiringSoonRegistries.length"
                class="text-cyan-600"
            >
                <li
                    v-for="registry in expiringSoonRegistries"
                    :key="registry.name"
                    class="truncate py-1"
                >
                    <Link
                        :href="
                            route('workspaces.registries.show', {
                                project: projectId,
                                workspace: workspace,
                                registry: registry.registry_id,
                            })
                        "
                    >
                        {{ registry.name }}
                    </Link>
                </li>
            </ul>
            <p v-else class="italic text-gray-400">
                None of the registries expiring within 30 days.
            </p>
        </aside>
    </section>
</template>

<style scoped></style>
