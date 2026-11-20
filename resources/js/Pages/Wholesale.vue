<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
const form = useForm({ business_name: '', contact_name: '', email: '', phone: '', city: '', monthly_quantity_kg: 50, message: '' });
function submit() { form.post('/wholesale', { preserveScroll: true, onSuccess: () => form.reset() }); }
</script>

<template>
    <Head title="Wholesale jaggery & gur | Golden Jaggery Co">
        <meta name="description" content="Ask Golden Jaggery Co about bulk and wholesale rates for shops, cafés and food businesses." />
    </Head>
    <section class="bg-[#f1e7d3] py-14 dark:bg-[#302a20]"><div class="container grid items-center gap-10 lg:grid-cols-2"><div><p class="eyebrow">For shops, cafés & food makers</p><h1 class="serif mt-3 text-5xl font-semibold leading-tight">Good gur belongs on every shelf.</h1><p class="mt-5 max-w-xl leading-8 text-stone-600 dark:text-stone-300">Stock fresh, family-made jaggery and shakkar for your customers. Tell us your monthly needs and our family will prepare a wholesale quote.</p><div class="mt-8 grid gap-3 sm:grid-cols-3"><div class="rounded-2xl bg-white/60 p-4 dark:bg-white/5"><strong class="serif text-lg">10–49 kg</strong><span class="mt-1 block text-xs text-stone-500">Growing stockists</span></div><div class="rounded-2xl bg-white/60 p-4 dark:bg-white/5"><strong class="serif text-lg">50–199 kg</strong><span class="mt-1 block text-xs text-stone-500">Regular buyers</span></div><div class="rounded-2xl bg-white/60 p-4 dark:bg-white/5"><strong class="serif text-lg">200+ kg</strong><span class="mt-1 block text-xs text-stone-500">Bulk enquiries</span></div></div><p class="mt-4 text-xs text-stone-500">Final wholesale prices depend on product, pack size, season and destination.</p></div>
            <form class="space-y-4 rounded-3xl bg-[var(--paper)] p-6 shadow-xl sm:p-8" @submit.prevent="submit"><h2 class="serif text-2xl font-semibold">Request wholesale pricing</h2>
                <label class="block text-sm font-semibold">Business name<input v-model="form.business_name" required class="form-control mt-2"><span v-if="form.errors.business_name" class="mt-1 block text-xs text-red-600">{{ form.errors.business_name }}</span></label>
                <div class="grid gap-4 sm:grid-cols-2"><label class="text-sm font-semibold">Your name<input v-model="form.contact_name" required class="form-control mt-2"></label><label class="text-sm font-semibold">Phone<input v-model="form.phone" required class="form-control mt-2"><span v-if="form.errors.phone" class="mt-1 block text-xs text-red-600">{{ form.errors.phone }}</span></label></div>
                <div class="grid gap-4 sm:grid-cols-2"><label class="text-sm font-semibold">Email<input v-model="form.email" type="email" class="form-control mt-2"></label><label class="text-sm font-semibold">City<input v-model="form.city" required class="form-control mt-2"></label></div>
                <label class="block text-sm font-semibold">Estimated monthly quantity (kg)<input v-model.number="form.monthly_quantity_kg" type="number" min="10" required class="form-control mt-2"><span v-if="form.errors.monthly_quantity_kg" class="mt-1 block text-xs text-red-600">{{ form.errors.monthly_quantity_kg }}</span></label>
                <label class="block text-sm font-semibold">Products or details<textarea v-model="form.message" rows="3" class="form-control mt-2"></textarea></label>
                <p v-if="$page.props.flash?.success" class="rounded-xl bg-[#e6eddc] p-3 text-sm text-cane" role="status">{{ $page.props.flash.success }}</p>
                <button class="button-primary w-full" :disabled="form.processing">{{ form.processing ? 'Sending…' : 'Request a quote →' }}</button>
            </form></div></section>
</template>
