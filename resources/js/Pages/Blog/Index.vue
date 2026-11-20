<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '../../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
defineProps({ posts: { type: Object, required: true } });
const fallback = '/images/recipe-tea.svg';
</script>

<template>
    <Head title="Recipes & journal | Golden Jaggery Co">
        <meta name="description" content="Discover recipes, serving ideas and stories about gur, jaggery and shakkar from Golden Jaggery Co." />
    </Head>
    <section class="container py-14 sm:py-20"><p class="eyebrow">A pinch of tradition, a spoonful of joy</p><h1 class="serif mt-3 text-5xl font-semibold">Recipes & journal</h1><p class="mt-4 max-w-xl leading-7 text-stone-600 dark:text-stone-300">A few ways we love to bring gur and shakkar to the table.</p>
        <div v-if="posts.data?.length" class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-3"><article v-for="post in posts.data" :key="post.id" class="product-card"><Link :href="`/recipes/${post.slug}`"><img :src="post.image || fallback" :alt="post.title" loading="lazy" class="aspect-[4/3] w-full object-cover"><div class="p-5"><p class="eyebrow">{{ post.category }}</p><h2 class="serif mt-2 text-2xl font-semibold">{{ post.title }}</h2><p class="mt-3 line-clamp-3 text-sm leading-6 text-stone-500">{{ post.excerpt }}</p><span class="mt-4 block text-sm font-bold text-cane">Read the story →</span></div></Link></article></div>
        <div v-else class="mt-9 rounded-3xl bg-[var(--paper)] px-6 py-14 text-center"><h2 class="serif text-2xl font-semibold">Our recipe notebook is getting ready</h2><p class="mt-2 text-stone-500">Check back for tea-time favourites, winter drinks and family sweets.</p></div>
    </section>
</template>
