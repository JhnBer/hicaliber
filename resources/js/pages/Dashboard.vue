<script setup lang="ts">
import {Head, router} from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref } from 'vue';
import {seed, clean} from '@/routes/api/properties';
import axios from "axios";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const props = defineProps<{
    propertyCount: number,
}>();

const processing = ref<boolean>(false);

const handleSeed = async () => {
    if (processing.value) {
        return
    }

    processing.value = true;

    await axios.post(seed().url);
    await router.reload({
        preserveScroll: true,
        preserveState: true,
        only: ['propertyCount'],
    });

    processing.value = false;
}

const pruneProperty = async () => {
    if (processing.value) {
        return
    }

    processing.value = true;

    await axios.post(clean().url);
    await router.reload({
        preserveScroll: true,
        preserveState: true,
        only: ['propertyCount'],
    });

    processing.value = false
}

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
                    <div class="size-full flex flex-col items-center gap-5 justify-center">
                        <el-button  @click="handleSeed" :disabled="processing">
                            Seed database
                        </el-button>
                        <div>
                            <el-button @click="pruneProperty" :disabled="processing">Clean property table</el-button>
                        </div>
                    </div>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="size-full flex flex-col justify-center items-center gap-5">
                        <el-text>Property entries count: </el-text>
                        <div class=" z-1 flex p-2 justify-center bg-white dark:bg-neutral-800 border-1 border-gray-200 rounded-md w-min min-w-12">
                            <el-text v-text="propertyCount" size="large" type="primary" />
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
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
