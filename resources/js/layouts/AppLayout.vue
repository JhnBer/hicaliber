<script setup lang="ts">
import {useEchoPublic} from "@laravel/echo-vue";
import {onMounted} from "vue";
import {useSystemChannel} from "@/composables/useSystemChannel";
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

onMounted(() => {
    const { channel } = useEchoPublic('system-public');

    if (channel) {
        const echoChannelInstance = channel();
        const { setChannel } = useSystemChannel();
        setChannel(echoChannelInstance);
    }
})

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
