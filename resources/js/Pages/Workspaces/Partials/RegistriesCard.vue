<script setup>
import { Link, usePage } from "@inertiajs/vue3";

defineProps([
    "workspaceId",
    "nonCompliantRegistries",
    "expiringRegistries",
    "registriesCount",
    "hasRegistries",
    "registryMetrics",
    "upToDateRegistriesCount",
    "nonCompliantRegistriesCount",
]);

const projectId = usePage().props.projectId;
</script>

<template>
    <section
        :class="{
            'border-green-600': registryMetrics === 100,
            'border-cyan-600': registryMetrics !== 100,
        }"
        class="flex-grow border bg-white p-4 text-gray-600 dark:bg-gray-700 dark:text-gray-400"
    >
        <article class="font-bold">
            <header
                class="flex items-center justify-between whitespace-nowrap border-b pb-2"
            >
                <h2 class="truncate">Registries</h2>
                <template v-if="registryMetrics === 100">
                    <span
                        class="mb-2 rounded bg-green-500 px-2 text-xs font-medium text-white"
                        >EXCELLENT</span
                    >
                </template>
                <template v-else-if="registryMetrics > 90">
                    <span
                        class="mb-2 rounded bg-green-300 px-2 text-xs font-medium text-white"
                        >GOOD</span
                    >
                </template>
                <template v-else-if="registryMetrics > 80">
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
                    {{ registryMetrics }}%
                    <span class="truncate text-xs">COMPLETED</span>
                </p>
                <div>
                    <p class="text-sm">
                        <span class="truncate text-green-600"
                            >{{ upToDateRegistriesCount }} up-to-date</span
                        >
                    </p>
                    <p class="text-sm">
                        <span class="truncate text-red-600"
                            >{{
                                nonCompliantRegistriesCount
                            }}
                            non-compliant</span
                        >
                    </p>
                </div>
            </div>
        </article>
        <p v-if="!hasRegistries" class="text-red-600">
            No registries assign to this workspace.
        </p>

        <div v-else>
            <aside class="py-2 text-xs">
                <h3 class="py-2 font-bold">Most Outdated:</h3>
                <ul
                    v-if="
                        nonCompliantRegistries && nonCompliantRegistries.length
                    "
                    class="text-cyan-600"
                >
                    <li
                        v-for="registry in nonCompliantRegistries"
                        :key="registry.name"
                        class="truncate py-1"
                    >
                        <Link
                            :href="
                                route('workspaces.registries.show', [
                                    workspaceId,
                                    registry.id,
                                ])
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
                    v-if="expiringRegistries && expiringRegistries.length"
                    class="text-cyan-600"
                >
                    <li
                        v-for="registry in expiringRegistries"
                        :key="registry.name"
                        class="truncate py-1"
                    >
                        <Link
                            :href="
                                route('workspaces.registries.show', [
                                    workspaceId,
                                    registry.id,
                                ])
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
        </div>
    </section>
</template>

<style scoped></style>
