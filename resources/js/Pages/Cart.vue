<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
defineProps({ items: { type: Array, default: () => [] }, subtotal: { type: Number, default: 0 } });

function update(item, quantity) {
    router.patch(`/cart/items/${item.variant.id}`, { quantity }, { preserveScroll: true });
}
function remove(item) {
    router.delete(`/cart/items/${item.variant.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="Your cart | Golden Jaggery Co" />
    <section class="container py-12 sm:py-16">
        <p class="eyebrow">Almost at your table</p><h1 class="serif mt-2 text-4xl font-semibold">Your basket</h1>
        <div v-if="items.length" class="mt-8 grid gap-8 lg:grid-cols-[1fr_350px]">
            <div class="space-y-4">
                <article v-for="item in items" :key="item.variant.id" class="flex flex-wrap items-center gap-4 rounded-3xl border border-brown/10 bg-[var(--paper)] p-4 sm:flex-nowrap sm:p-5">
                    <img :src="item.variant.product.images?.[0] || '/images/gur-block.svg'" :alt="item.variant.product.name" class="h-24 w-24 rounded-2xl object-cover">
                    <div class="min-w-[140px] flex-1"><Link :href="`/products/${item.variant.product.slug}`" class="serif text-xl font-semibold">{{ item.variant.product.name }}</Link><p class="mt-1 text-sm text-stone-500">{{ item.variant.size }} · PKR {{ Number(item.variant.price).toLocaleString() }}</p><span v-if="item.variant.product.status === 'pre_order'" class="status-pill pre-order mt-2">Pre-order</span></div>
                    <div class="flex items-center gap-2"><button class="grid h-9 w-9 place-items-center rounded-full border" aria-label="Decrease quantity" :disabled="item.quantity <= 1" @click="update(item, item.quantity - 1)">−</button><span class="w-6 text-center font-bold">{{ item.quantity }}</span><button class="grid h-9 w-9 place-items-center rounded-full border" aria-label="Increase quantity" @click="update(item, item.quantity + 1)">+</button></div>
                    <div class="w-28 text-right font-bold">PKR {{ Number(item.line_total).toLocaleString() }}<button class="mt-2 block w-full text-xs text-stone-500 underline" @click="remove(item)">Remove</button></div>
                </article>
            </div>
            <aside class="h-fit rounded-3xl bg-[#efe5d2] p-6 dark:bg-[#302a20]">
                <h2 class="serif text-2xl font-semibold">A quick summary</h2>
                <div class="mt-6 flex justify-between text-sm"><span>Subtotal</span><strong>PKR {{ Number(subtotal).toLocaleString() }}</strong></div>
                <p class="mt-2 text-xs leading-5 text-stone-500">Delivery charges are calculated at checkout using the delivery destination.</p>
                <div class="my-5 border-t border-brown/15"></div>
                <Link href="/checkout" class="button-primary w-full">Continue to checkout →</Link>
                <Link href="/shop" class="mt-3 block text-center text-sm underline">Keep browsing</Link>
            </aside>
        </div>
        <div v-else class="mt-8 rounded-3xl bg-[var(--paper)] px-6 py-16 text-center"><div class="text-5xl">🧺</div><h2 class="serif mt-4 text-2xl font-semibold">Your basket is waiting for something sweet</h2><p class="mt-2 text-stone-500">Find your favourite gur, shakkar or a little batch to share.</p><Link href="/shop" class="button-primary mt-6">Explore the shop</Link></div>
    </section>
</template>
