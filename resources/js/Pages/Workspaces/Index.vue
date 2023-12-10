<script setup>

import {Head, Link, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref, watch} from "vue";
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";

const props = defineProps({
  paginatedWorkspaces: {
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
  router.get(route('workspaces.index', {project: projectId}), index.value, {
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
      <h2 class="text-white dark:text-gray-700 leading-tight">Workspaces</h2>
    </template>
    <div class="px-2 pb-2 ">
      <div class="p-6 shadow overflow-x-auto bg-white">
        <div class="flex items-center justify-between">
          <div class="mb-2 flex items-center">
            <input v-model="index.search" type="text" name="search" placeholder="Search…"
                   class="text-sm h-8 px-6 py-2 border-gray-200 ">
            <button type="button"
                    class="ml-3 text-sm text-gray-500 hover:text-gray-700 focus:text-cyan-600"
                    @click="resetSearch">Reset
            </button>

          </div>
          <Link :href="route('workspaces.create', projectId)" class="text-cyan-600 hover:text-cyan-700 text-sm">
            Create
            Workspaces
          </Link>
        </div>
        <div class="relative overflow-x-auto">
          <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-2" @click="sort('name')">
                Name
                <i :class="getSortIconClass('name')"></i>
              </th>

              <th scope="col" class="px-6 py-2" @click="sort('location')">
                Location
                <i :class="getSortIconClass('location')"></i>
              </th>

            </tr>
            </thead>
            <tbody>
            <tr v-for="(workspace, index) in paginatedWorkspaces.data" :key="workspace.id"
                :class="{'bg-white dark:bg-gray-800': true, 'border-b dark:border-gray-700': index !== paginatedWorkspaces.data.length - 1}">

              <th scope="row"
                  class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                <Link :href="route('workspaces.edit', [ projectId, workspace.id])"
                      class="text-cyan-600 hover:text-cyan-700">
                  {{ workspace.name }}
                </Link>
              </th>

              <td class="px-6 py-2">
                {{ workspace.location }}
              </td>


            </tr>

            </tbody>
          </table>
        </div>

        <Pagination :links="paginatedWorkspaces.links" class="flex items-center justify-end py-2"></Pagination>
      </div>
    </div>

  </AuthenticatedLayout>

</template>

<style scoped>

</style>
