<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
const props = defineProps({ order: { type: Object, default: null }, notFound: { type: Boolean, default: false } });
const form = useForm({ order_number: '', phone: '' });
const states = ['pending', 'confirmed', 'packed', 'shipped', 'delivered'];
function submit() { form.post('/track-order', { preserveScroll: true }); }
</script>

<template>
    <Head title="Track your order | Golden Jaggery Co" />
    <section class="container max-w-2xl py-14 sm:py-20">
        <p class="eyebrow">We’ll keep you posted</p><h1 class="serif mt-3 text-4xl font-semibold">Track your order</h1><p class="mt-3 leading-7 text-stone-600 dark:text-stone-300">Enter your order number and the phone number used at checkout to see the latest update.</p>
        <form class="mt-7 space-y-4 rounded-3xl border border-brown/10 bg-[var(--paper)] p-6" @submit.prevent="submit">
            <label class="block text-sm font-semibold">Order number<input v-model="form.order_number" required class="form-control mt-2" placeholder="GJ…"><span v-if="form.errors.order_number" class="mt-1 block text-xs text-red-600">{{ form.errors.order_number }}</span></label>
            <label class="block text-sm font-semibold">Phone number<input v-model="form.phone" required autocomplete="tel" class="form-control mt-2" placeholder="+92 …"><span v-if="form.errors.phone" class="mt-1 block text-xs text-red-600">{{ form.errors.phone }}</span></label>
            <button type="submit" class="button-primary w-full" :disabled="form.processing">Find my order</button>
        </form>
        <p v-if="notFound" class="mt-5 rounded-2xl bg-[#f7e6dc] p-4 text-sm text-[#7b3b22]" role="alert">We couldn’t find an order matching those details. Please check the number and phone, then try again.</p>
        <article v-if="order" class="mt-7 rounded-3xl bg-[var(--paper)] p-6">
            <div class="flex flex-wrap justify-between gap-3"><h2 class="serif text-2xl font-semibold">{{ order.order_number }}</h2><span class="status-pill">{{ order.status }}</span></div>
            <p class="mt-1 text-sm text-stone-500">{{ order.city }}, {{ order.country }}</p>
            <ol class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-5">
                <li v-for="state in states" :key="state" class="rounded-2xl p-3 text-center text-xs capitalize" :class="states.indexOf(order.status) >= states.indexOf(state) ? 'bg-[#e6eddc] font-bold text-cane' : 'bg-stone-100 text-stone-400 dark:bg-white/5'"><span class="mb-1 block text-lg">{{ states.indexOf(order.status) >= states.indexOf(state) ? '✓' : '·' }}</span>{{ state }}</li>
            </ol>
            <p v-if="order.tracking_number" class="mt-5 text-sm">Tracking number: <strong>{{ order.tracking_number }}</strong></p>
            <p v-if="order.status === 'cancelled'" class="mt-4 rounded-xl bg-[#f7e6dc] p-3 text-sm text-[#7b3b22]">This order has been cancelled. Please contact our team if you need help.</p>
        </article>
    </section>
</template>
