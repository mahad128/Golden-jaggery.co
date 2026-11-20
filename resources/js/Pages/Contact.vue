<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
const brand = usePage().props.brand;
const form = useForm({ name: '', email: '', phone: '', subject: '', message: '' });
function submit() { form.post('/contact', { preserveScroll: true, onSuccess: () => form.reset() }); }
</script>

<template>
    <Head title="Contact us | Golden Jaggery Co">
        <meta name="description" content="Get in touch with Golden Jaggery Co about jaggery, gur, delivery, wholesale and your order." />
    </Head>
    <section class="container py-14 sm:py-20">
        <div class="grid gap-10 lg:grid-cols-[.8fr_1.2fr]">
            <div><p class="eyebrow">A real family is here to help</p><h1 class="serif mt-3 text-5xl font-semibold">Let’s talk gur.</h1><p class="mt-4 leading-7 text-stone-600 dark:text-stone-300">Questions about a fresh batch, delivery or a special order? Send us a note and we’ll get back to you.</p>
                <div class="mt-8 space-y-4 rounded-3xl bg-[#efe5d2] p-6 dark:bg-[#302a20]"><div><span class="eyebrow">Call or WhatsApp</span><a :href="`tel:${brand.phone}`" class="mt-1 block font-semibold">{{ brand.phone }}</a></div><div><span class="eyebrow">Email</span><a :href="`mailto:${brand.email}`" class="mt-1 block font-semibold">{{ brand.email }}</a></div><div><span class="eyebrow">Made in</span><p class="mt-1 font-semibold">{{ brand.location }}</p></div><a v-if="brand.whatsapp_enabled && brand.whatsapp_number" :href="`https://wa.me/${brand.whatsapp_number}`" class="button-primary w-full">Message us on WhatsApp</a></div>
                <a class="mt-4 block overflow-hidden rounded-3xl border border-brown/10 bg-[var(--paper)] p-5 text-sm" :href="`https://maps.google.com/?q=${encodeURIComponent(brand.location)}`" target="_blank" rel="noreferrer"><strong class="serif text-lg">Find our production home</strong><span class="mt-1 block text-stone-500">Open the location in Google Maps ↗</span></a>
            </div>
            <form class="space-y-4 rounded-3xl border border-brown/10 bg-[var(--paper)] p-6 sm:p-8" @submit.prevent="submit">
                <h2 class="serif mb-4 text-2xl font-semibold">Send us a message</h2>
                <label class="block text-sm font-semibold">Your name<input v-model="form.name" required class="form-control mt-2"><span v-if="form.errors.name" class="mt-1 block text-xs text-red-600">{{ form.errors.name }}</span></label>
                <div class="grid gap-4 sm:grid-cols-2"><label class="text-sm font-semibold">Email<input v-model="form.email" type="email" class="form-control mt-2"><span v-if="form.errors.email" class="mt-1 block text-xs text-red-600">{{ form.errors.email }}</span></label><label class="text-sm font-semibold">Phone (optional)<input v-model="form.phone" class="form-control mt-2"></label></div>
                <label class="block text-sm font-semibold">Subject<input v-model="form.subject" required class="form-control mt-2"><span v-if="form.errors.subject" class="mt-1 block text-xs text-red-600">{{ form.errors.subject }}</span></label>
                <label class="block text-sm font-semibold">How can we help?<textarea v-model="form.message" required rows="5" class="form-control mt-2"></textarea><span v-if="form.errors.message" class="mt-1 block text-xs text-red-600">{{ form.errors.message }}</span></label>
                <p v-if="$page.props.flash?.success" class="rounded-xl bg-[#e6eddc] p-3 text-sm text-cane" role="status">{{ $page.props.flash.success }}</p>
                <button class="button-primary w-full" type="submit" :disabled="form.processing">{{ form.processing ? 'Sending…' : 'Send message →' }}</button>
            </form>
        </div>
    </section>
</template>
