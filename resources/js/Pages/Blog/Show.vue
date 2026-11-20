<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import SiteLayout from '../../Components/SiteLayout.vue';
import { translate } from '../../translations.js';

defineOptions({ layout: SiteLayout });
const props = defineProps({ post: { type: Object, required: true } });
const locale = computed(() => usePage().props.locale ?? 'en');
const postTitle = computed(() => locale.value === 'ur' && props.post.title_ur ? props.post.title_ur : props.post.title);
</script>

<template>
    <Head :title="`${postTitle} | Golden Jaggery Co`"><meta name="description" :content="post.excerpt"></Head>
    <article class="container max-w-3xl py-12 sm:py-16"><Link href="/recipes" class="text-sm font-semibold text-cane">{{ translate(locale, 'all_recipes') }}</Link><p class="eyebrow mt-8">{{ post.category }}</p><h1 class="serif mt-3 text-4xl font-semibold sm:text-5xl">{{ postTitle }}</h1><p class="mt-4 text-stone-500">{{ post.published_at ? new Date(post.published_at).toLocaleDateString() : '' }}</p><img v-if="post.image" :src="post.image" :alt="postTitle" class="mt-8 max-h-[460px] w-full rounded-3xl object-cover"><p class="mt-8 text-lg leading-8 text-stone-600 dark:text-stone-300">{{ post.excerpt }}</p><div class="mt-6 whitespace-pre-line leading-8 text-stone-700 dark:text-stone-200">{{ post.body }}</div><Link href="/shop" class="button-primary mt-9">{{ translate(locale, 'find_favourite_gur') }}</Link></article>
</template>
