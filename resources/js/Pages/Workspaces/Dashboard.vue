<script setup>
import {Head, Link, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  workspace: {
    type: Object,
  },
  project: {
    type: Object
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
    type: Number
  },
  countOfExpiredRegistries: {
    type: Number
  },
  percentageOfUpToDate: {
    type: Number
  }
})

const page = usePage();

const projectId = usePage().props.auth.user.project_id;

</script>

<template>
  <Head title="Workspace"/>

  <AuthenticatedLayout :workspace="workspace">
    <template #header>
      <h2 class="text-white dark:text-gray-700 leading-tight">Dashboard</h2>
    </template>

    <div class="px-2 pb-2 sm:flex sm:space-x-2">


      <section
          :class="{'border-green-600': percentageOfUpToDate === 100, 'border-cyan-600': percentageOfUpToDate !== 100}"
          class="border flex-grow p-4 rounded-lg bg-white  shadow-2xl  text-gray-600 mt-2">
        <article class="font-bold">
          <header class="justify-between items-center flex border-b pb-2 block whitespace-nowrap ">
            <h2 class="truncate">Registries</h2>
            <template v-if="percentageOfUpToDate === 100">
              <span class="bg-green-500 px-2 mb-2 rounded text-white text-xs font-medium">EXCELLENT</span>
            </template>
            <template v-else-if="percentageOfUpToDate > 90">
              <span class="bg-green-300 px-2 mb-2 rounded text-white text-xs font-medium">GOOD</span>
            </template>
            <template v-else-if="percentageOfUpToDate > 80">
              <span class="bg-yellow-300 px-2 mb-2 rounded text-white text-xs font-medium">AVERAGE</span>
            </template>
            <template v-else>
                    <span
                        class="bg-red-500 px-2 mb-2 rounded text-white text-xs font-medium truncate">NEEDS IMPROVEMENT</span>
            </template>
          </header>

          <div class="flex justify-between py-2">
            <p class="text-5xl">{{ percentageOfUpToDate }}% <span class="text-xs truncate">COMPLETED</span></p>
            <div>
              <p class="text-sm"><span class="text-green-600 truncate">{{
                  countOfUpToDateRegistries
                }} valid</span></p>
              <p class="text-sm"><span class="text-red-600 truncate">{{ countOfExpiredRegistries }} expired</span>
              </p>
            </div>
          </div>

        </article>


        <aside class=" text-xs py-2">
          <h3 class="py-2 font-bold">Most Outdated:</h3>
          <ul v-if="mostOutdatedRegistries && mostOutdatedRegistries.length" class="text-cyan-600">
            <li v-for="registry in mostOutdatedRegistries" :key="registry.name" class="py-1 truncate">
              <Link
                  :href="route('workspace.registries.show', { project: projectId, workspace: workspace, registry: registry.registry_id})">
                {{ registry.name }}
              </Link>

            </li>
          </ul>
          <p v-else class="text-green-600 italic">All of the registries are updated.</p>
        </aside>
        <aside class=" text-xs py-2">
          <h3 class="py-2 font-bold">Expiring Soon:</h3>
          <ul v-if="expiringSoonRegistries && expiringSoonRegistries.length" class="text-cyan-600 ">
            <li v-for="registry in expiringSoonRegistries" :key="registry.name" class="py-1 truncate">
              <Link
                  :href="route('workspace.registries.show', { project: projectId, workspace: workspace, registry: registry.registry_id})">
                {{ registry.name }}
              </Link>
            </li>
          </ul>
          <p v-else class="text-gray-400 italic">None of the registries expiring within 30 days.</p>
        </aside>
      </section>


      <section
          :class="{'border-green-600': percentageOfUpToDate === 100, 'border-cyan-600': percentageOfUpToDate !== 100}"
          class="border flex-grow p-4 rounded-lg shadow-2xl bg-white  text-gray-600 mt-2">
        <article class="font-bold">
          <header class="justify-between items-center flex border-b pb-2 block whitespace-nowrap ">
            <h2 class="truncate">Registries</h2>
            <template v-if="percentageOfUpToDate === 100">
              <span class="bg-green-500 px-2 mb-2 rounded text-white text-xs font-medium">EXCELLENT</span>
            </template>
            <template v-else-if="percentageOfUpToDate > 90">
              <span class="bg-green-300 px-2 mb-2 rounded text-white text-xs font-medium">GOOD</span>
            </template>
            <template v-else-if="percentageOfUpToDate > 80">
              <span class="bg-yellow-300 px-2 mb-2 rounded text-white text-xs font-medium">AVERAGE</span>
            </template>
            <template v-else>
                    <span
                        class="bg-red-500 px-2 mb-2 rounded text-white text-xs font-medium truncate">NEEDS IMPROVEMENT</span>
            </template>
          </header>

          <div class="flex justify-between py-2">
            <p class="text-5xl">{{ percentageOfUpToDate }}% <span class="text-xs truncate">COMPLETED</span></p>
            <div>
              <p class="text-sm"><span class="text-green-600 truncate">{{
                  countOfUpToDateRegistries
                }} valid</span></p>
              <p class="text-sm"><span class="text-red-600 truncate">{{ countOfExpiredRegistries }} expired</span>
              </p>
            </div>
          </div>

        </article>


        <aside class=" text-xs py-2">
          <h3 class="py-2 font-bold">Most Outdated:</h3>
          <ul v-if="mostOutdatedRegistries && mostOutdatedRegistries.length" class="text-cyan-600">
            <li v-for="registry in mostOutdatedRegistries" :key="registry.name" class="py-1 truncate">

              {{ registry.name }}

            </li>
          </ul>
          <p v-else class="text-green-600 italic">All of the registries are updated.</p>
        </aside>
        <aside class=" text-xs py-2">
          <h3 class="py-2 font-bold">Expiring Soon:</h3>
          <ul v-if="expiringSoonRegistries && expiringSoonRegistries.length" class="text-cyan-600 ">
            <li v-for="registry in expiringSoonRegistries" :key="registry.name" class="py-1 truncate">

              {{ registry.name }}

            </li>
          </ul>
          <p v-else class="text-gray-400 italic">None of the registries expiring within 30 days.</p>
        </aside>
      </section>

    </div>
  </AuthenticatedLayout>
</template>

