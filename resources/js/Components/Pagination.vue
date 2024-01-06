<script setup>

import {Link} from "@inertiajs/vue3";
import {defineProps} from "vue";

const props = defineProps({
    links: Object,
})

</script>

<template>
    <div>

        <template v-for="(link, index) in links" :key="index" class="flex items-center">
            <div v-if="link.url === null"
                 class="px-2 py-1 bg-white text-gray-200 text-xs"
                 v-html="link.label"/>
            <Link v-else
                  :href="link.url"
                  v-html="link.label"
                  preserve-scroll
                  :class="`px-2 py-1 text-xs disabled:opacity-50 disabled:cursor-not-allowed ${link.active ? 'bg-gray-600 text-white' : 'bg-white hover:bg-gray-100 text-gray-600'}`"/>
        </template>
    </div>
</template>

