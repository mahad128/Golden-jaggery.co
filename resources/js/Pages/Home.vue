<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ProductCard from '../Components/ProductCard.vue';
import ProductScene from '../Components/ProductScene.vue';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
defineProps({
    products: { type: Array, default: () => [] },
    reviews: { type: Array, default: () => [] },
    posts: { type: Array, default: () => [] },
    content: { type: Object, default: () => ({}) },
});

const categories = [
    { title: 'Gur blocks', image: '/images/gur-block.svg', alt: 'Golden blocks of fresh gur' },
    { title: 'Gur powder', image: '/images/gur-powder.svg', alt: 'Soft, golden gur powder' },
    { title: 'Pure shakkar', image: '/images/shakkar.svg', alt: 'Unrefined cane sugar crystals' },
];

const gallery = [
    { image: '/images/cane-field.jpg', alt: 'A green sugarcane field', label: 'Sugarcane fields' },
    { image: '/images/gur-making.jpg', alt: 'A worker stirring a sugarcane preparation by hand', label: 'Gur made by hand' },
    { image: '/images/shakkar-crystals.jpg', alt: 'Close-up of golden-brown sugar crystals', label: 'Golden sugar crystals' },
];

const steps = [
    { number: '01', image: '/images/cane-field.jpg', alt: 'Ripe sugarcane growing in the field before harvest', art: 'photo', title: 'Harvested with care', text: 'We cut ripe sugarcane from our own fields at just the right time.' },
    { number: '02', image: '/images/cane-press.svg', alt: 'A traditional press squeezing fresh sugarcane juice', art: 'scene', title: 'Pressed while fresh', text: 'Fresh cane juice is pressed and filtered in small family batches.' },
    { number: '03', image: '/images/gur-cooking.svg', alt: 'Golden gur slowly cooking in a traditional open pan', art: 'scene', title: 'Slow-cooked to golden', text: 'Traditional open-pan cooking brings out gur’s deep, comforting flavour.' },
    { number: '04', image: '/images/gur-parcel-sealed.svg', alt: 'A sealed parcel of fresh gur ready for delivery', art: 'scene', title: 'Packed for your home', text: 'Every batch is cooled, hand-checked and packed fresh for your table.' },
];
</script>

<template>
    <Head title="Pure Jaggery & Gur | Golden Jaggery Co">
        <meta name="description" content="Pure, natural, chemical-free jaggery and gur made by our family. Shop fresh gur blocks, gur powder and shakkar, delivered across Pakistan." />
    </Head>

    <section class="hero-stage" aria-labelledby="hero-title">
        <span class="hero-word" aria-hidden="true">
            <span v-for="(letter, index) in 'PURE'" :key="letter" class="hero-letter" :style="{ animationDelay: `${index * 80}ms` }">{{ letter }}</span>
        </span>
        <span class="hero-badge" aria-hidden="true">FAMILY<br>MADE<br>WITH CARE</span>
        <div class="floating-leaf floating-leaf-one" aria-hidden="true"></div>
        <div class="floating-leaf floating-leaf-two" aria-hidden="true"></div>

        <div class="hero-scene">
            <ProductScene label="Golden gur block" photo="/images/jaggery-cutout.png" />
        </div>

        <div class="hero-copy">
            <p class="eyebrow mb-3">Small-batch · Pure · Family-made</p>
            <h1 id="hero-title" class="display-font text-3xl font-semibold leading-tight sm:text-4xl">
                {{ content.hero_title || 'Fresh and Pure Jaggery' }}
            </h1>
            <p class="mt-3 text-sm leading-6 text-stone-600 sm:text-base">
                {{ content.hero_subtitle || 'Pure. Natural. Chemical-free jaggery that nourishes your body.' }}
            </p>
        </div>

        <div class="hero-promise">
            <p class="text-sm font-medium leading-6 text-stone-600 sm:text-base">
                Fresh from our family fields to your home, across Pakistan and beyond.
            </p>
            <Link href="/shop" class="button-primary">Buy online <span aria-hidden="true">↗</span></Link>
        </div>
    </section>

    <section class="container section-space" aria-labelledby="category-title">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Find your favourite</p>
                <h2 id="category-title" class="display-font mt-2 text-4xl font-semibold uppercase tracking-wide sm:text-5xl">Categories</h2>
            </div>
            <Link href="/shop" class="button-light">Explore all products <span class="ml-2">→</span></Link>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            <Link
                v-for="(category, index) in categories"
                :key="category.title"
                href="/shop"
                class="category-tile reveal"
                :style="{ '--category-delay': `${index * 110}ms` }"
            >
                <img :src="category.image" :alt="category.alt" loading="lazy">
                <span>{{ category.title }} <span aria-hidden="true">↗</span></span>
            </Link>
        </div>
    </section>

    <section class="container pb-20" aria-labelledby="featured-title">
        <div class="mb-9 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Goodness in every grain</p>
                <h2 id="featured-title" class="serif mt-2 text-4xl font-semibold">Our family favourites</h2>
            </div>
            <Link href="/shop" class="button-light">Shop jaggery & gur <span class="ml-2">→</span></Link>
        </div>
        <div v-if="products.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
        </div>
        <div v-else class="rounded-3xl border border-dashed border-brown/20 p-10 text-center text-stone-500">
            Our next fresh batch is coming soon. Check back for seasonal gur.
        </div>
    </section>

    <section id="gallery" class="bg-[#f0e5cd] py-16 dark:bg-[#302a20]" aria-labelledby="gallery-title">
        <div class="container">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">A little look behind the sweetness</p>
                    <h2 id="gallery-title" class="serif mt-2 text-4xl font-semibold">From sugarcane to sweetness</h2>
                </div>
                <Link href="/about" class="button-light">Our family story <span class="ml-2">→</span></Link>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <figure v-for="item in gallery" :key="item.label" class="reveal relative aspect-[4/3] overflow-hidden rounded-3xl">
                    <img :src="item.image" :alt="item.alt" loading="lazy" decoding="async" class="gallery-photo h-full w-full object-cover">
                    <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-5 pb-5 pt-14 font-semibold text-white">{{ item.label }}</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section class="container section-space" aria-labelledby="story-title">
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <p class="eyebrow">From cane to gur</p>
            <h2 id="story-title" class="serif mt-3 text-4xl font-semibold sm:text-5xl">{{ content.story_title || 'Good things take a little time.' }}</h2>
            <p class="mt-4 leading-7 text-stone-600 dark:text-stone-300">{{ content.story_text || 'Every golden batch follows the same honest journey—no shortcuts, just our family’s hands-on care.' }}</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article v-for="step in steps" :key="step.number" class="reveal rounded-3xl border border-brown/10 bg-[var(--paper)] p-6">
                <span class="serif text-4xl text-amber">{{ step.number }}</span>
                <div class="process-art my-5" :class="`process-art-${step.art}`">
                    <img
                        :src="step.image"
                        :alt="step.alt"
                        :class="step.art === 'scene' ? 'process-art-scene-image' : 'process-art-photo-image'"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
                <h3 class="serif text-xl font-semibold">{{ step.title }}</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600 dark:text-stone-300">{{ step.text }}</p>
            </article>
        </div>
    </section>

    <section class="container pb-20">
        <div class="grid gap-5 md:grid-cols-3">
            <article class="rounded-3xl bg-cane p-7 text-white">
                <span class="text-3xl">🌾</span>
                <h3 class="serif mt-5 text-2xl">From our own cane</h3>
                <p class="mt-3 text-sm leading-6 text-white/75">We grow, harvest and craft in small batches, so every pack has a family story behind it.</p>
            </article>
            <article class="rounded-3xl border border-brown/10 bg-[var(--paper)] p-7">
                <span class="text-3xl">✨</span>
                <h3 class="serif mt-5 text-2xl">Nothing unnecessary</h3>
                <p class="mt-3 text-sm leading-6 text-stone-600 dark:text-stone-300">Honest, unrefined sweetness with the rich taste of real sugarcane.</p>
            </article>
            <article class="rounded-3xl border border-brown/10 bg-[var(--paper)] p-7">
                <span class="text-3xl">🚚</span>
                <h3 class="serif mt-5 text-2xl">Sent with care</h3>
                <p class="mt-3 text-sm leading-6 text-stone-600 dark:text-stone-300">Freshly packed and delivered to homes across Pakistan, with international delivery available.</p>
            </article>
        </div>
    </section>

    <section v-if="reviews.length" class="bg-[#efe5d2] py-16 dark:bg-[#302a20]" aria-labelledby="reviews-title">
        <div class="container">
            <div class="mb-9 text-center">
                <p class="eyebrow">Kind words from our table</p>
                <h2 id="reviews-title" class="serif mt-2 text-4xl font-semibold">Made sweeter by you</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <figure v-for="review in reviews" :key="review.id" class="rounded-3xl bg-[var(--paper)] p-6">
                    <div class="mb-3 text-amber" aria-label="5 stars">★★★★★</div>
                    <blockquote class="leading-7">“{{ review.body }}”</blockquote>
                    <figcaption class="mt-5 text-sm font-bold">{{ review.customer_name }}</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section class="container section-space">
        <div class="flex flex-col items-center justify-between gap-8 rounded-[32px] bg-[#e7d5b6] p-8 dark:bg-[#383022] md:flex-row md:p-12">
            <div class="max-w-2xl">
                <p class="eyebrow">A sweeter way to stock up</p>
                <h2 class="serif mt-3 text-4xl font-semibold">Looking for gur by the box?</h2>
                <p class="mt-4 leading-7 text-stone-600 dark:text-stone-300">We work with shops, cafés and families who love buying in bulk. Tell us what you need and we’ll make a fresh quote.</p>
            </div>
            <Link href="/wholesale" class="button-primary shrink-0">Ask about wholesale →</Link>
        </div>
    </section>
</template>
