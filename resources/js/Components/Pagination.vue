<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps(["links"]);
</script>

<template>
    <div>
        <template
            v-for="(link, index) in links"
            :key="index"
            class="flex items-center"
        >
            <div
                v-if="link.url === null"
                class="px-2 py-1 text-xs text-gray-300"
                v-html="link.label"
            />

            <Link
                v-else
                :href="link.url"
                v-html="link.label"
                preserve-scroll
                :class="`px-2 py-1 text-xs disabled:cursor-not-allowed disabled:opacity-50 ${link.active ? 'bg-gray-600 text-white' : 'text-gray-600 hover:bg-gray-100'}`"
            >
            </Link>
        </template>
    </div>
</template>
