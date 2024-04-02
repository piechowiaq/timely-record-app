<script setup>
import {Link, usePage} from "@inertiajs/vue3";

defineProps([
    'workspaceId',
    'nonCompliantRegistries',
    'expiringRegistries',
    'registriesCount',
    'hasRegistries',
    'registryMetrics',
    'upToDateRegistriesCount',
    'nonCompliantRegistriesCount'
]);

const projectId = usePage().props.projectId;

</script>

<template>

    <section
        :class="{'border-green-600': registryMetrics === 100, 'border-cyan-600': registryMetrics !== 100}"
        class="border flex-grow p-4 bg-white text-gray-600 dark:bg-gray-700 dark:text-gray-400">
        <article class="font-bold">
            <header class="justify-between items-center flex border-b pb-2 block whitespace-nowrap ">
                <h2 class="truncate">Registries</h2>
                <template v-if="registryMetrics === 100">
                    <span class="bg-green-500 px-2 mb-2 rounded text-white text-xs font-medium">EXCELLENT</span>
                </template>
                <template v-else-if="registryMetrics > 90">
                    <span class="bg-green-300 px-2 mb-2 rounded text-white text-xs font-medium">GOOD</span>
                </template>
                <template v-else-if="registryMetrics > 80">
                    <span class="bg-yellow-300 px-2 mb-2 rounded text-white text-xs font-medium">AVERAGE</span>
                </template>
                <template v-else>
                    <span
                        class="bg-red-500 px-2 mb-2 rounded text-white text-xs font-medium truncate">NEEDS IMPROVEMENT</span>
                </template>
            </header>

            <div class="flex justify-between py-2">
                <p class="text-5xl">{{ registryMetrics }}% <span class="text-xs truncate">COMPLETED</span></p>
                <div>
                    <p class="text-sm"><span class="text-green-600 truncate">{{
                            upToDateRegistriesCount
                        }} up-to-date</span></p>
                    <p class="text-sm"><span class="text-red-600 truncate">{{
                            nonCompliantRegistriesCount
                        }} non-compliant</span>
                    </p>
                </div>
            </div>

        </article>
        <p v-if="!hasRegistries" class="text-red-600">No registries assign to this workspace.</p>

        <div v-else>
            <aside class=" text-xs py-2">
                <h3 class="py-2 font-bold">Most Outdated:</h3>
                <ul v-if="nonCompliantRegistries && nonCompliantRegistries.length" class="text-cyan-600">
                    <li v-for="registry in nonCompliantRegistries" :key="registry.name" class="py-1 truncate">
                        <Link
                            :href="route('workspaces.registries.show', [workspaceId,registry.id])">
                            {{ registry.name }}
                        </Link>

                    </li>
                </ul>
                <p v-else class="text-green-600 italic">All of the registries are updated.</p>
            </aside>
            <aside class=" text-xs py-2">
                <h3 class="py-2 font-bold">Expiring Soon:</h3>
                <ul v-if="expiringRegistries && expiringRegistries.length" class="text-cyan-600 ">
                    <li v-for="registry in expiringRegistries" :key="registry.name" class="py-1 truncate">
                        <Link
                            :href="route('workspaces.registries.show', [workspaceId, registry.id])">
                            {{ registry.name }}
                        </Link>
                    </li>
                </ul>
                <p v-else class="text-gray-400 italic">None of the registries expiring within 30 days.</p>
            </aside>

        </div>

    </section>

</template>

<style scoped>

</style>
