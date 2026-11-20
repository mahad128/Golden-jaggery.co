<script setup>
import { computed, reactive, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
const props = defineProps({ products: { type: Object, default: () => ({ data: [], links: [] }) }, filters: { type: Object, default: () => ({}) }, types: { type: Array, default: () => [] } });
const productRows = computed(() => props.products.data ?? []);
const form = reactive({ q: props.filters.q ?? '', type: props.filters.type ?? '', weight: props.filters.weight ?? '', max_price: props.filters.max_price ?? '', sort: props.filters.sort ?? 'featured' });

watch(() => props.filters, (next) => Object.assign(form, { q: next.q ?? '', type: next.type ?? '', weight: next.weight ?? '', max_price: next.max_price ?? '', sort: next.sort ?? 'featured' }));
function applyFilters() {
    router.get('/shop', Object.fromEntries(Object.entries(form).filter(([, value]) => value !== '')), { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Shop Pure Jaggery, Gur & Shakkar">
        <meta name="description" content="Browse fresh family-made jaggery, gur blocks, gur powder and shakkar in 500 g, 1 kg and 5 kg packs." />
    </Head>
    <section class="bg-[#f1e7d3] py-14 dark:bg-[#302a20]"><div class="container"><p class="eyebrow">Straight from our family</p><h1 class="serif mt-3 text-5xl font-semibold">Shop jaggery & gur</h1><p class="mt-4 max-w-xl leading-7 text-stone-600 dark:text-stone-300">Find your new kitchen favourite, made from our own sugarcane and packed fresh in small batches.</p></div></section>
    <section class="container py-10">
        <form class="mb-8 grid gap-3 rounded-3xl border border-brown/10 bg-[var(--paper)] p-4 sm:grid-cols-2 lg:grid-cols-6" @submit.prevent="applyFilters">
            <label class="sr-only" for="shop-search">Search products</label><input id="shop-search" v-model="form.q" class="form-control" placeholder="Search products">
            <label class="sr-only" for="shop-type">Product type</label><select id="shop-type" v-model="form.type" class="form-control"><option value="">All types</option><option v-for="type in types" :key="type" :value="type">{{ type }}</option></select>
            <label class="sr-only" for="shop-weight">Pack size</label><select id="shop-weight" v-model="form.weight" class="form-control"><option value="">All sizes</option><option value="500">500 g</option><option value="1000">1 kg</option><option value="5000">5 kg</option></select>
            <label class="sr-only" for="shop-price">Maximum price in PKR</label><input id="shop-price" v-model="form.max_price" type="number" min="1" class="form-control" placeholder="Max price (PKR)">
            <label class="sr-only" for="shop-sort">Sort by</label><select id="shop-sort" v-model="form.sort" class="form-control"><option value="featured">Featured</option><option value="price_low">Price: low to high</option><option value="price_high">Price: high to low</option></select>
            <button type="submit" class="button-primary">Find your gur</button>
        </form>
        <div class="mb-5 flex items-center justify-between text-sm text-stone-500"><span>{{ products.total ?? 0 }} products</span><Link v-if="filters.q || filters.type || filters.weight || filters.max_price" href="/shop" class="underline">Clear filters</Link></div>
        <div v-if="productRows.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <ProductCard v-for="product in productRows" :key="product.id" :product="product" />
        </div>
        <nav v-if="products.links?.length > 3" class="mt-10 flex flex-wrap justify-center gap-2" aria-label="Product pages">
            <template v-for="link in products.links" :key="link.label">
                <Link v-if="link.url" :href="link.url" class="rounded-full border px-4 py-2 text-sm" :class="link.active ? 'border-cane bg-cane text-white' : 'border-brown/15'">{{ link.label.replace('&laquo;', '‹').replace('&raquo;', '›') }}</Link>
                <span v-else class="rounded-full border border-brown/10 px-4 py-2 text-sm text-stone-400">{{ link.label.replace('&laquo;', '‹').replace('&raquo;', '›') }}</span>
            </template>
        </nav>
        <div v-if="!productRows.length" class="rounded-3xl bg-[var(--paper)] px-6 py-16 text-center"><p class="serif text-2xl font-semibold">Nothing on the shelf just yet</p><p class="mt-2 text-stone-500">Try another search or get in touch about our next fresh batch.</p><Link href="/contact" class="button-primary mt-6">Ask our family</Link></div>
    </section>
</template>
