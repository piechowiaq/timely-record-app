<script setup>
import { router } from "@inertiajs/vue3";
import {
    computed,
    onBeforeMount,
    onBeforeUnmount,
    onBeforeUpdate,
    onErrorCaptured,
    onMounted,
    onRenderTracked,
    onRenderTriggered,
    onUpdated,
    reactive,
} from "vue";
import { useUserStore } from "@/Stores/UserStore.js";

const props = defineProps(["workspaces"]);

const userStore = useUserStore();

onBeforeMount(() => {
    console.log("onBeforeMount");
});

onBeforeUpdate(() => {
    console.log("onBeforeUpdate");
    unwatch();
});

onBeforeUnmount(() => {
    console.log("onBeforeUnmount");
});

onMounted(() => {
    console.log("onMounted");
});

onUpdated(() => {
    console.log("onUpdated");
});

onErrorCaptured(() => {
    console.log("onErrorCaptured");
});

onRenderTracked(() => {
    console.log("onRenderTracked");
});

onRenderTriggered(() => {
    console.log("onRenderTriggered");
});

const form = reactive({
    first_name: "",
    last_name: userStore.getLastName,
});

const firstName = computed({
    get: () => form.first_name, // Assuming getFirstName is a method
    set: (value) => {
        userStore.setFirstName(value);
        form.first_name = value; // Assuming you want to update the form as well
    },
});

const lastName = computed({
    get: () => userStore.getLastName, // Assuming getLastName is a method
    set: (value) => {
        userStore.setLastName(value);
        form.last_name = value; // Assuming you want to update the form as well
    },
});

function submit() {
    router.post(route("users.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>Hello</template>

<style scoped></style>
