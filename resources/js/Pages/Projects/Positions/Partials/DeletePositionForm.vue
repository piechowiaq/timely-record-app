<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";

const confirmingPositionDeletion = ref(false);
const passwordInput = ref(null);

const props = defineProps(["position"]);

const form = useForm({
    password: "",
});

const projectId = usePage().props.projectId;

const confirmPositionDeletion = () => {
    confirmingPositionDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deletePosition = () => {
    form.delete(route("positions.destroy", props.position.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingPositionDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section>
        <DangerButton @click="confirmPositionDeletion"
            >Delete Position
        </DangerButton>

        <Modal :show="confirmingPositionDeletion" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    Are you sure you want to delete this Position?
                </h2>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="deletePosition"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deletePosition"
                    >
                        Delete Position
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
