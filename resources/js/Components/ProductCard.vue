<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { translate } from '../translations.js';

const props = defineProps({ product: { type: Object, required: true } });
const locale = usePage().props.locale ?? 'en';
const firstVariant = computed(() => props.product.active_variants?.[0]);
const photo = computed(() => props.product.images?.[0] ?? '/images/gur-block.svg');
const statusLabel = computed(() => translate(locale, `status_${props.product.status}`));
const productName = computed(() => locale === 'ur' && props.product.name_ur ? props.product.name_ur : props.product.name);
</script>

<template>
    <article class="product-card group reveal">
        <Link :href="`/products/${product.slug}`" class="block">
            <div class="relative aspect-[4/3] overflow-hidden bg-[#eee1c8]">
                <img :src="photo" :alt="productName" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                <span class="status-pill absolute left-4 top-4" :class="{ 'pre-order': product.status === 'pre_order', 'out-of-season': product.status === 'out_of_season' }">{{ statusLabel }}</span>
            </div>
            <div class="p-5">
                <p class="eyebrow mb-2">{{ product.type }}</p>
                <h3 class="serif text-xl font-semibold">{{ productName }}</h3>
                <p class="mt-2 line-clamp-2 min-h-10 text-sm leading-6 text-stone-500">{{ product.short_description }}</p>
                <div class="mt-5 flex items-center justify-between gap-2">
                    <span class="font-bold text-cane">From PKR {{ Number(firstVariant?.price ?? 0).toLocaleString() }}</span>
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-cream text-cane transition group-hover:bg-cane group-hover:text-white" aria-hidden="true">↗</span>
                </div>
            </div>
        </Link>
    </article>
</template>
