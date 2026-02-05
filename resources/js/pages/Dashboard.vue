<script setup lang="ts">
import {Head} from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { onMounted, ref} from 'vue';
import {seed, clean, importMethod} from '@/routes/api/properties';
import axios from "axios";
import {useSystemChannel} from "@/composables/useSystemChannel";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const props = defineProps<{
    propertyCount: number,
    importActive: boolean,
}>();

const processing = ref<boolean>(false);

const { publicChannel: systemChannel } = useSystemChannel();

const handleSeed = async () => {
    if (processing.value) {
        return
    }

    processing.value = true;

    await axios.post(seed().url);

    processing.value = false;
}

const pruneProperty = async () => {
    if (processing.value) {
        return
    }

    processing.value = true;

    await axios.post(clean().url);

    processing.value = false
}

const handleImport = async () => {
    if (processing.value) {
        return
    }

    processing.value = true;

    await axios.post(importMethod().url);

    processing.value = false;
}

const count = ref<number>(props.propertyCount);
const canImport = ref<boolean>(!props.importActive)

onMounted(() => {
    if (systemChannel.value) {
        systemChannel.value.listenToAll((e, data) => {
            if (e === '.PropertyCountUpdated') {
                count.value = data.count;
            }

            if (e === '.ImportStatusChanged') {
                canImport.value = !data.active;
            }
        })
    }
})

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="size-full flex flex-col gap-5 justify-center pl-10">
                        <div>
                            <el-button @click="handleSeed" :disabled="processing || !canImport">
                                Seed database
                            </el-button>
                        </div>
                        <div>
                            <el-button @click="handleImport" :disabled="processing || !canImport">
                                Import example
                            </el-button>
                        </div>
                        <div>
                            <el-button @click="pruneProperty" :disabled="processing || !canImport">Clean property table</el-button>
                        </div>
                    </div>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="size-full flex flex-col justify-center items-center gap-5">
                        <el-text>Property entries count: </el-text>
                        <div class=" z-1 flex p-2 justify-center bg-white dark:bg-neutral-800 border-1 border-gray-200 rounded-md w-min min-w-12">
                            <el-text v-text="count" size="large" type="primary" />
                        </div>
                    </div>
                    <PlaceholderPattern class="absolute size-full" />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <div class="size-full p-5">
                    Click "seed database" to generate 100 random properties.
                    <br>
                    Click "clean property table" to clear property table.
                    <br>
                    Both actions trigger an event that updates the property counter and the search table data.
                </div>
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
