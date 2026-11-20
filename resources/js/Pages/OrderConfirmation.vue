<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
defineProps({ order: { type: Object, required: true } });
const brand = usePage().props.brand;
</script>

<template>
    <Head :title="`Order ${order.order_number} received`" />
    <section class="container max-w-3xl py-16 text-center sm:py-24">
        <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-[#e6eddc] text-4xl text-cane">✓</div>
        <p class="eyebrow mt-6">A little sweetness is on its way</p>
        <h1 class="serif mt-3 text-4xl font-semibold">Thank you, {{ order.customer_name }}!</h1>
        <p class="mt-4 leading-7 text-stone-600 dark:text-stone-300">We’ve received your order. Our family will get in touch to confirm the delivery details.</p>
        <div class="mt-8 rounded-3xl bg-[var(--paper)] p-6 text-left">
            <div class="flex flex-wrap justify-between gap-3"><span class="text-stone-500">Order number</span><strong class="text-lg">{{ order.order_number }}</strong></div>
            <div class="my-4 border-t border-brown/10"></div>
            <div v-for="item in order.items" :key="item.id" class="flex justify-between gap-3 py-1 text-sm"><span>{{ item.product_name }} · {{ item.variant_name }} × {{ item.quantity }}</span><strong>PKR {{ Number(item.line_total).toLocaleString() }}</strong></div>
            <div class="my-4 border-t border-brown/10"></div>
            <div class="flex justify-between"><span>Order total</span><strong>PKR {{ Number(order.total).toLocaleString() }}</strong></div>
            <p class="mt-2 text-sm text-stone-500">Payment: {{ order.payment_method === 'cash_on_delivery' ? 'Cash on delivery' : 'Bank transfer' }}</p>
            <p v-if="order.payment_method === 'bank_transfer'" class="mt-3 rounded-xl bg-[#fff0cf] p-3 text-sm leading-6 text-[#805313]"><strong>Bank transfer details:</strong><br>{{ brand.bank_transfer_instructions }}</p>
        </div>
        <div class="mt-8 flex flex-wrap justify-center gap-3"><Link href="/track-order" class="button-primary">Track this order</Link><Link href="/shop" class="button-light">Back to the shop</Link></div>
    </section>
</template>
