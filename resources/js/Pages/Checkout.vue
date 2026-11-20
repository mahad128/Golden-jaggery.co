<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import SiteLayout from '../Components/SiteLayout.vue';

defineOptions({ layout: SiteLayout });
const props = defineProps({ items: { type: Array, default: () => [] }, subtotal: { type: Number, default: 0 }, shippingRates: { type: Array, default: () => [] }, paymentMethods: { type: Array, default: () => [] } });
const form = useForm({ customer_name: '', phone: '', email: '', address: '', city: '', country: 'Pakistan', delivery_method: 'domestic', payment_method: 'cash_on_delivery', discount_code: '', customer_note: '' });
const brand = computed(() => usePage().props.brand);
const quotedDiscount = ref(0);
const quotedCode = ref('');
const discountMessage = ref('');
const discountProcessing = ref(false);
const matchedRate = computed(() => props.shippingRates.find((rate) => {
    if (form.delivery_method === 'domestic') {
        const domesticRates = props.shippingRates.filter((rate) => rate.scope === 'city' && rate.country.toLowerCase() === 'pakistan');
        return domesticRates.find((rate) => rate.city && rate.city.toLowerCase() === form.city.trim().toLowerCase())
            ?? domesticRates.find((rate) => !rate.city);
    }

    return rate.scope === 'country' && rate.country.toLowerCase() === form.country.trim().toLowerCase();
}) ?? null);
const discountNeedsQuote = computed(() => form.discount_code.trim() !== '' && form.discount_code.trim().toUpperCase() !== quotedCode.value);
const canPlaceOrder = computed(() => Boolean(matchedRate.value) && !discountNeedsQuote.value && !form.processing && (form.delivery_method !== 'international' || form.payment_method !== 'cash_on_delivery'));
const estimatedTotal = computed(() => Math.max(0, props.subtotal + (matchedRate.value?.amount ?? 0) - quotedDiscount.value));

watch(() => form.delivery_method, (method) => {
    quotedDiscount.value = 0;
    discountMessage.value = '';
    if (method === 'international') {
        form.country = '';
        form.payment_method = 'bank_transfer';
    } else {
        form.country = 'Pakistan';
    }
});
watch(() => form.discount_code, () => {
    quotedDiscount.value = 0;
    quotedCode.value = '';
    discountMessage.value = '';
});

function submit() {
    form.post('/checkout');
}

async function applyDiscount() {
    discountMessage.value = '';
    quotedDiscount.value = 0;
    quotedCode.value = '';
    if (!form.discount_code.trim()) return;

    discountProcessing.value = true;
    try {
        const response = await fetch('/checkout/discount', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify({ discount_code: form.discount_code }),
        });
        const result = await response.json();
        if (!response.ok) {
            discountMessage.value = result.errors?.discount_code?.[0] ?? 'We could not apply that code.';
            return;
        }

        quotedDiscount.value = result.discount;
        quotedCode.value = result.code;
        discountMessage.value = `${result.code} applied`;
    } catch (error) {
        discountMessage.value = 'We could not check that code right now. Please try again.';
    } finally {
        discountProcessing.value = false;
    }
}
</script>

<template>
    <Head title="Checkout | Golden Jaggery Co">
        <meta name="description" content="Order pure desi jaggery and gur from Golden Jaggery Co. Secure cash on delivery or bank transfer checkout." />
    </Head>
    <section class="container py-12">
        <p class="eyebrow">Freshness, on its way</p><h1 class="serif mt-2 text-4xl font-semibold">Checkout</h1>
        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_380px]">
            <form class="space-y-7" @submit.prevent="submit">
                <section class="rounded-3xl border border-brown/10 bg-[var(--paper)] p-5 sm:p-7">
                    <h2 class="serif mb-5 text-2xl font-semibold">Where should we send your order?</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="text-sm font-semibold">Full name<input v-model="form.customer_name" required autocomplete="name" class="form-control mt-2" placeholder="Your name"><span v-if="form.errors.customer_name" class="mt-1 block text-xs text-red-600">{{ form.errors.customer_name }}</span></label>
                        <label class="text-sm font-semibold">Phone / WhatsApp<input v-model="form.phone" required autocomplete="tel" class="form-control mt-2" placeholder="+92 ..."><span v-if="form.errors.phone" class="mt-1 block text-xs text-red-600">{{ form.errors.phone }}</span></label>
                        <label class="text-sm font-semibold sm:col-span-2">Email (optional)<input v-model="form.email" type="email" autocomplete="email" class="form-control mt-2" placeholder="you@example.com"><span v-if="form.errors.email" class="mt-1 block text-xs text-red-600">{{ form.errors.email }}</span></label>
                        <label class="text-sm font-semibold sm:col-span-2">Street address<textarea v-model="form.address" required autocomplete="street-address" rows="2" class="form-control mt-2" placeholder="House, street, area"></textarea><span v-if="form.errors.address" class="mt-1 block text-xs text-red-600">{{ form.errors.address }}</span></label>
                        <label class="text-sm font-semibold">City<input v-model="form.city" required autocomplete="address-level2" class="form-control mt-2" placeholder="Your city"><span v-if="form.errors.city" class="mt-1 block text-xs text-red-600">{{ form.errors.city }}</span></label>
                        <label class="text-sm font-semibold">Country<input v-model="form.country" required autocomplete="country-name" :readonly="form.delivery_method === 'domestic'" class="form-control mt-2" placeholder="Country"><span v-if="form.errors.country" class="mt-1 block text-xs text-red-600">{{ form.errors.country }}</span></label>
                    </div>
                </section>
                <section class="rounded-3xl border border-brown/10 bg-[var(--paper)] p-5 sm:p-7">
                    <h2 class="serif mb-5 text-2xl font-semibold">Delivery & payment</h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex cursor-pointer gap-3 rounded-2xl border p-4" :class="form.delivery_method === 'domestic' ? 'border-cane bg-cane/5' : 'border-brown/10'"><input v-model="form.delivery_method" type="radio" value="domestic"><span><strong>Pakistan delivery</strong><small class="mt-1 block text-stone-500">Fresh delivery across Pakistan</small></span></label>
                        <label class="flex cursor-pointer gap-3 rounded-2xl border p-4" :class="form.delivery_method === 'international' ? 'border-cane bg-cane/5' : 'border-brown/10'"><input v-model="form.delivery_method" type="radio" value="international"><span><strong>International</strong><small class="mt-1 block text-stone-500">Country-wise shipping rates</small></span></label>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <label v-for="method in paymentMethods.filter((method) => form.delivery_method === 'domestic' || method !== 'cash_on_delivery')" :key="method" class="flex cursor-pointer items-center gap-3 rounded-2xl border p-4" :class="form.payment_method === method ? 'border-cane bg-cane/5' : 'border-brown/10'"><input v-model="form.payment_method" type="radio" :value="method"><span><strong>{{ method === 'cash_on_delivery' ? 'Cash on delivery' : 'Bank transfer' }}</strong><small class="mt-1 block text-stone-500">{{ method === 'cash_on_delivery' ? 'Pay when your order arrives' : 'We’ll share transfer details after ordering' }}</small></span></label>
                    </div>
                    <p v-if="form.errors.payment_method" class="mt-2 text-xs text-red-600">{{ form.errors.payment_method }}</p>
                    <p class="mt-4 text-xs leading-5 text-stone-500">JazzCash, Easypaisa, Stripe and PayPal can be added when payment accounts are ready.</p>
                </section>
                <section class="rounded-3xl border border-brown/10 bg-[var(--paper)] p-5 sm:p-7">
                    <h2 class="serif mb-4 text-2xl font-semibold">Anything else we should know?</h2>
                    <label class="text-sm font-semibold">Discount code<input v-model="form.discount_code" class="form-control mt-2" placeholder="If you have one"></label>
                    <button class="mt-2 text-sm font-bold text-cane underline" type="button" :disabled="discountProcessing" @click="applyDiscount">{{ discountProcessing ? 'Checking…' : 'Apply discount code' }}</button>
                    <p v-if="discountMessage" class="mt-1 text-xs" :class="quotedDiscount ? 'text-cane' : 'text-red-600'" role="status">{{ discountMessage }}</p>
                    <p v-if="form.errors.discount_code" class="mt-1 text-xs text-red-600">{{ form.errors.discount_code }}</p>
                    <label class="mt-4 block text-sm font-semibold">Order note (optional)<textarea v-model="form.customer_note" rows="2" class="form-control mt-2" placeholder="Delivery instructions or a note for our family"></textarea></label>
                </section>
            </form>
            <aside class="h-fit rounded-3xl bg-[#efe5d2] p-6 dark:bg-[#302a20] lg:sticky lg:top-28">
                <h2 class="serif text-2xl font-semibold">Order summary</h2>
                <div class="mt-5 space-y-3">
                    <div v-for="item in items" :key="item.variant.id" class="flex justify-between gap-3 text-sm"><span>{{ item.variant.product.name }} · {{ item.variant.size }} × {{ item.quantity }}</span><strong class="whitespace-nowrap">PKR {{ Number(item.line_total).toLocaleString() }}</strong></div>
                </div>
                <div class="my-5 border-t border-brown/15"></div>
                <div class="flex justify-between"><span>Subtotal</span><strong>PKR {{ Number(subtotal).toLocaleString() }}</strong></div>
                <div class="mt-2 flex justify-between text-sm"><span>Delivery</span><strong v-if="matchedRate">PKR {{ Number(matchedRate.amount).toLocaleString() }}</strong><span v-else class="text-amber">Rate not available</span></div>
                <p class="mt-2 text-xs leading-5 text-stone-500">Delivery rates are managed by our team. If your destination has no active rate, contact us for a quote before ordering.</p>
                <div v-if="quotedDiscount" class="mt-2 flex justify-between text-sm text-cane"><span>Discount</span><strong>− PKR {{ Number(quotedDiscount).toLocaleString() }}</strong></div>
                <div class="mt-3 flex justify-between border-t border-brown/15 pt-3"><span>Total</span><strong>PKR {{ Number(estimatedTotal).toLocaleString() }}</strong></div>
                <p v-if="form.errors.cart" class="mt-3 text-sm text-red-600">{{ form.errors.cart }}</p>
                <p v-if="form.errors.city" class="mt-3 text-sm text-red-600">{{ form.errors.city }}</p>
                <button class="button-primary mt-6 w-full" type="button" :disabled="!canPlaceOrder" @click="submit">{{ form.processing ? 'Placing your order…' : 'Place my order →' }}</button>
                <Link href="/cart" class="mt-3 block text-center text-sm underline">Back to basket</Link>
                <p class="mt-5 text-center text-xs leading-5 text-stone-500">Need help? <a :href="`mailto:${brand.email}`" class="underline">{{ brand.email }}</a></p>
            </aside>
        </div>
    </section>
</template>
