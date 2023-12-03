<script setup>

import {Head, Link, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref, watch} from "vue";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const props = defineProps({
  paginatedRegistries: {
    type: Object,
  },
  filters: {
    type: Object,
  },
});


const projectId = usePage().props.auth.user.project_id;

const index = ref({
  search: props.filters.search,
});

watch(index.value, debounce(() => {
  router.get(route('project.registries.index', {project: projectId}), index.value, {
    preserveState: true,
    replace: true
  });
}, 300));

const sort = (field) => {
  index.value.field = field;
  index.value.direction = index.value.direction === 'asc' ? 'desc' : 'asc';
}

const resetSearch = () => {
  index.value.search = '';
}

const getSortIconClass = (field) => {
  if (index.value.field !== field) {
    return 'fa-solid fa-sort fa-xs ml-2'; // Default icon when the field is not the current sort field
  }
  return index.value.direction === 'asc' ? 'fa-solid fa-sort-down fa-xs ml-2' : 'fa-solid fa-sort-up fa-xs ml-2';
};


</script>

<template>
  <Head title="Project"/>

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-white dark:text-gray-700 leading-tight">Registries</h2>
    </template>
    <div class="px-2 pb-2 ">
      <div class="p-6 shadow overflow-x-auto bg-white">
        <div class="flex items-center justify-between">
          <div class="mb-2 flex items-center">
            <input v-model="index.search" type="text" name="search" placeholder="Search…"
                   class="text-sm h-8 px-6 py-3 border-gray-200 ">
            <button type="button"
                    class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
                    @click="resetSearch">Reset
            </button>

          </div>
          <Link :href="route('project.registries.create', projectId)" class="text-cyan-600 hover:text-cyan-700 text-sm">
            Create
            Custom Registry
          </Link>
        </div>
        <div class="relative overflow-x-auto">
          <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-3" @click="sort('name')">
                Name
                <i :class="getSortIconClass('name')"></i>
              </th>

              <th scope="col" class="px-6 py-3" @click="sort('validity_period')">
                Valid in months
                <i :class="getSortIconClass('validity_period')"></i>
              </th>

              <th scope="col" class="px-6 py-3 text-center" @click="sort('project_id')">
                Type
                <i :class="getSortIconClass('project_id')"></i>
              </th>

            </tr>
            </thead>
            <tbody>
            <tr v-for="(registry, index) in paginatedRegistries.data" :key="registry.id"
                :class="{'bg-white dark:bg-gray-800': true, 'border-b dark:border-gray-700': index !== paginatedRegistries.data.length - 1}">

              <th scope="row"
                  class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                <Link :href="route('project.registries.edit', [ projectId, registry.id])"
                      class="text-cyan-600 hover:text-cyan-700">
                  {{ registry.name }}
                </Link>

              </th>

              <td class="px-6 py-4">
                {{ registry.validity_period }}
              </td>
              <td class="px-6 py-4 text-center flex justify-center">
                <ApplicationLogo v-if="registry.project_id === null"
                                 class="w-4 h-4 fill-white stroke-2"></ApplicationLogo>
                <p v-else class="italic text-xs">custom</p>
              </td>


            </tr>

            </tbody>
          </table>
        </div>

        <Pagination :links="paginatedRegistries.links" class="flex items-center justify-end py-2"></Pagination>
      </div>
    </div>

  </AuthenticatedLayout>

</template>

<style scoped>

</style>
