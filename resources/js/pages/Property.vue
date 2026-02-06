<script setup lang="ts">
import {Head} from '@inertiajs/vue3';
import axios from 'axios';
import { LoaderCircle } from "lucide-vue-next";
import type { CSSProperties} from "vue";
import {computed, onMounted, reactive, ref, watch} from "vue";
import {useSystemChannel} from "@/composables/useSystemChannel";
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { search } from '@/routes/api/properties';
import { index as property } from '@/routes/properties';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Property',
        href: property().url,
    }
];

const properties = ref<any[]>([]);
const loading = ref(false);
const meta = ref({ current_page: 1, last_page: 1 });
const rangeOrInputs = ref<boolean>(false)
const priceRanges = {
    min: 100000,
    max: 500000,
}
const { publicChannel: systemChannel } = useSystemChannel();

interface Mark {
    style: CSSProperties
    label: string
}

type Marks = Record<number, Mark | string>
const marks: Marks = {};
for (let i = priceRanges.min; i <= priceRanges.max; i += 50000) {
    marks[i] = (i / 1000).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + 'k';
}
marks[priceRanges.max] = {
    style: {
        color: '#1989FA',
    },
    label: 'Unlimited',
};


const filters = reactive({
    name: '',
    bedrooms: null,
    bathrooms: null,
    storeys: null,
    garages: null,
    min_price: null as number | null,
    max_price: null as number | null,
    page: 1
});

const genColumns = () => {
    return [
        {
            key: 'name',
            dataKey: 'name',
            title: 'Name',
            width: 200,
        },
        {
            key: 'price',
            dataKey: 'price',
            title: 'Price',
            width: 150,
        },
        {
            key: 'bedrooms',
            dataKey: 'bedrooms',
            title: 'Bedrooms',
            width: 100,
        },
        {
            key: 'bathrooms',
            dataKey: 'bathrooms',
            title: 'Bathrooms',
            width: 100,
        },
        {
            key: 'storeys',
            dataKey: 'storeys',
            title: 'Storeys',
            width: 100,
        },
        {
            key: 'garages',
            dataKey: 'garages',
            title: 'Garages',
            width: 100,
        },
    ];
}

const columns = genColumns();

const testRange = computed({
    get: () => [filters.min_price || priceRanges.min, filters.max_price || priceRanges.max],
    set: (value) => {
        filters.min_price = value[0];
        filters.max_price = value[1] === priceRanges.max ? null : value[1];
    }
});

const fetchProperties = async (replace = true) => {
    loading.value = true;
    try {
        const response = await axios.get(search().url, {
            params: filters,
        });

        if (replace) {
            properties.value = response.data.data;
        } else {
            properties.value.push(...response.data.data);
        }

        meta.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page
        };
    } finally {
        loading.value = false;
    }
};

const handleSearch = () => {
    filters.page = 1;
    fetchProperties(true);
};

const loadMore = () => {
    if (filters.page < meta.value.last_page) {
        filters.page++;
        fetchProperties(false);
    }
};

onMounted(() => {
    fetchProperties();

    if (systemChannel.value) {
        systemChannel.value.listenToAll((e) => {
            if (e === '.PropertyCountUpdated') {
                fetchProperties();
            }
        })
    }
});

watch([() => filters.min_price, () => filters.max_price], ([newMin, newMax]) => {
    if (newMin !== null || newMax !== null) {
        if (!rangeOrInputs.value) {
            handleSearch();
        }
    }
});
</script>

<template>
    <Head title="Property" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mb-4 grid grid-cols-6 grid-rows-auto gap-4 p-4 rounded shadow">
            <el-input class="col-start-1 col-end-3" v-model="filters.name" placeholder="Search by name" @input="handleSearch" />
            <div class="flex gap-4 col-start-3 col-end-7">
                <el-input-number v-model="filters.bedrooms" placeholder="Bedrooms" :min="0" :max="10" @change="handleSearch" />
                <el-input-number v-model="filters.bathrooms" placeholder="Bathrooms" :min="0" :max="10" @change="handleSearch" />
                <el-input-number v-model="filters.storeys" placeholder="Storeys" :min="0" :max="10" @change="handleSearch" />
                <el-input-number v-model="filters.garages" placeholder="Garages" :min="0" :max="10" @change="handleSearch" />
            </div>

            <el-switch
                v-model="rangeOrInputs"
                class="col-start-1 col-end-3"
                active-text="Use slider"
                inactive-text="Set manually"
            />

            <div class="col-start-3 col-end-5">
                <el-slider v-if="rangeOrInputs"
                           v-model="testRange"
                           range
                           :marks="marks"
                           :min="priceRanges.min" :max="priceRanges.max"
                           @change="handleSearch"
                />
                <div v-else class="flex gap-4">
                    <el-input-number v-model="filters.min_price" :min="priceRanges.min" placeholder="Min Price" />
                    <el-input-number v-model="filters.max_price" placeholder="Max Price" />
                </div>
            </div>

        </div>
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <el-auto-resizer>
                <template #default="{ height, width }">
                    <el-table-v2
                        :columns="columns"
                        :data="properties"
                        :width="width"
                        :height="height"
                        fixed
                        @end-reached="(e) => {console.log(e); loadMore()}"
                    >
                        <template #overlay v-if="loading">
                            <div
                                class="el-loading-mask"
                                style="display: flex; align-items: center; justify-content: center"
                            >
                                <el-icon class="is-loading" color="var(--el-color-primary)" :size="26">
                                    <LoaderCircle/>
                                </el-icon>
                            </div>
                        </template>
                    </el-table-v2>
                </template>
            </el-auto-resizer>

        </div>
    </AppLayout>
</template>
<style>
.example-showcase .el-table-v2__overlay {
    z-index: 9;
}
</style>
