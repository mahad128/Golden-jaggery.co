<script setup>
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import ProductScene from '../Components/ProductScene.vue';
import SiteLayout from '../Components/SiteLayout.vue';
import { translate } from '../translations.js';

defineOptions({ layout: SiteLayout });
const props = defineProps({ product: { type: Object, required: true } });
const locale = usePage().props.locale ?? 'en';
const variants = computed(() => props.product.active_variants ?? []);
const selected = ref(variants.value[0]?.id ?? null);
const quantity = ref(1);
const activeImage = ref(0);
const scene = ref(null);
const photos = computed(() => props.product.images?.length ? props.product.images : []);
const selectedVariant = computed(() => variants.value.find((variant) => variant.id === selected.value));
const productName = computed(() => locale === 'ur' && props.product.name_ur ? props.product.name_ur : props.product.name);
const productDescription = computed(() => locale === 'ur' && props.product.description_ur ? props.product.description_ur : (props.product.description || props.product.short_description));
const structuredData = computed(() => JSON.stringify({
    '@context': 'https://schema.org/',
    '@type': 'Product',
    name: `${props.product.name} (jaggery / gur)`,
    description: props.product.seo_description || props.product.short_description,
    image: photos.value,
    offers: variants.value.map((variant) => ({ '@type': 'Offer', priceCurrency: 'PKR', price: variant.price, availability: props.product.status === 'out_of_season' ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' })),
}));
const status = computed(() => translate(locale, `status_${props.product.status}`));

function addToCart() {
    if (!selected.value) return;
    router.post('/cart/items', { variant_id: selected.value, quantity: quantity.value }, { preserveScroll: true });
}

function swipe(delta) {
    if (!photos.value.length) return;
    activeImage.value = (activeImage.value + delta + photos.value.length) % photos.value.length;
}
</script>

<template>
    <Head :title="`${product.name} | Pure Jaggery & Gur`">
        <meta name="description" :content="product.seo_description || product.short_description">
        <script head-key="product-jsonld" type="application/ld+json" v-text="structuredData"></script>
    </Head>
    <div class="container py-8 text-sm text-stone-500"><a href="/shop" class="hover:text-cane">{{ translate(locale, 'shop') }}</a> <span class="mx-2">/</span> {{ productName }}</div>
    <section class="container grid gap-10 pb-16 lg:grid-cols-2">
        <div>
            <div class="relative overflow-hidden rounded-[32px] bg-gradient-to-br from-[#ead4ab] via-[#f4e8d0] to-[#dfc18d] dark:from-[#493a25] dark:via-[#322b20] dark:to-[#5a4426]" @touchstart.passive="(event) => $event.currentTarget.dataset.touchX = event.touches[0].clientX" @touchend.passive="(event) => { const delta = event.changedTouches[0].clientX - Number(event.currentTarget.dataset.touchX || 0); if (Math.abs(delta) > 40) swipe(delta > 0 ? -1 : 1); }">
                <div class="absolute inset-0"><img v-if="photos.length" :src="photos[activeImage]" :alt="productName" class="h-full w-full object-cover opacity-25"></div>
                <div class="relative h-[360px] sm:h-[480px]"><ProductScene ref="scene" :kind="product.type === 'shakkar' ? 'pile' : 'gur'" :label="product.name" />
                    <div class="absolute bottom-4 right-4 flex gap-2"><button class="grid h-10 w-10 place-items-center rounded-full bg-white/85 text-lg shadow" aria-label="Zoom in" @click="scene?.zoomIn()">+</button><button class="grid h-10 w-10 place-items-center rounded-full bg-white/85 text-lg shadow" aria-label="Zoom out" @click="scene?.zoomOut()">−</button></div>
                </div>
                <button class="absolute left-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/80 text-xl" aria-label="Previous image" @click="swipe(-1)">‹</button>
                <button class="absolute right-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-white/80 text-xl" aria-label="Next image" @click="swipe(1)">›</button>
            </div>
            <div v-if="photos.length > 1" class="mt-3 flex gap-3 overflow-x-auto">
                <button v-for="(photo, index) in photos" :key="photo" class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border-2" :class="activeImage === index ? 'border-amber' : 'border-transparent'" :aria-label="`Show product image ${index + 1}`" @click="activeImage = index"><img :src="photo" :alt="`${product.name} photo ${index + 1}`" class="h-full w-full object-cover"></button>
            </div>
            <p class="mt-2 text-center text-xs text-stone-500">Drag with your pointer, or tilt your phone to explore the 3D view.</p>
        </div>
        <div class="py-2">
            <p class="eyebrow">{{ product.type }}</p>
            <h1 class="serif mt-3 text-4xl font-semibold sm:text-5xl">{{ productName }} <span class="text-amber">· jaggery / gur</span></h1>
            <p class="mt-5 leading-8 text-stone-600 dark:text-stone-300">{{ product.short_description }}</p>
            <span class="status-pill mt-5" :class="{ 'pre-order': product.status === 'pre_order', 'out-of-season': product.status === 'out_of_season' }">{{ status }}</span>
            <div class="mt-8">
                <label for="variant" class="mb-2 block text-sm font-bold">{{ translate(locale, 'choose_pack') }}</label>
                <select id="variant" v-model="selected" class="form-control">
                    <option v-for="variant in variants" :key="variant.id" :value="variant.id">{{ variant.size }} — PKR {{ Number(variant.price).toLocaleString() }}{{ product.status === 'in_stock' ? ` · ${variant.stock} available` : '' }}</option>
                </select>
            </div>
            <div class="mt-5 flex items-center gap-4">
                <label class="sr-only" for="quantity">{{ translate(locale, 'quantity') }}</label><input id="quantity" v-model.number="quantity" type="number" min="1" max="30" class="form-control max-w-24">
                <strong class="text-2xl">PKR {{ Number(selectedVariant?.price ?? 0).toLocaleString() }}</strong>
            </div>
            <button type="button" class="button-primary mt-6 w-full sm:w-auto" :disabled="!selected || product.status === 'out_of_season'" @click="addToCart">{{ translate(locale, 'add_to_cart') }} <span aria-hidden="true">→</span></button>
            <p class="mt-3 text-xs leading-5 text-stone-500">{{ translate(locale, 'secure_checkout') }}</p>
            <div class="mt-9 border-t border-brown/10 pt-7">
                <h2 class="serif text-2xl font-semibold">{{ translate(locale, 'more_about_batch') }}</h2>
                <p class="mt-3 whitespace-pre-line leading-7 text-stone-600 dark:text-stone-300">{{ productDescription }}</p>
                <div v-if="product.benefits?.length" class="mt-5"><h3 class="font-bold">{{ translate(locale, 'why_we_love_it') }}</h3><ul class="mt-2 list-inside list-disc space-y-1 text-sm text-stone-600 dark:text-stone-300"><li v-for="benefit in product.benefits" :key="benefit">{{ benefit }}</li></ul></div>
                <div v-if="product.how_to_use?.length" class="mt-5"><h3 class="font-bold">{{ translate(locale, 'ways_to_enjoy_it') }}</h3><ul class="mt-2 list-inside list-disc space-y-1 text-sm text-stone-600 dark:text-stone-300"><li v-for="idea in product.how_to_use" :key="idea">{{ idea }}</li></ul></div>
            </div>
            <div v-if="product.reviews?.length" class="mt-9 border-t border-brown/10 pt-7">
                <h2 class="serif text-2xl font-semibold">{{ translate(locale, 'customer_notes') }}</h2>
                <blockquote v-for="review in product.reviews" :key="review.id" class="mt-4 rounded-2xl bg-[var(--paper)] p-4 text-sm leading-6">“{{ review.body }}” <strong class="mt-2 block">{{ review.customer_name }}</strong></blockquote>
            </div>
        </div>
    </section>
</template>
