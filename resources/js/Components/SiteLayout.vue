<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import BrandMark from './BrandMark.vue';
import { translate } from '../translations.js';

const page = usePage();
const brand = computed(() => page.props.brand);
const cartCount = computed(() => page.props.cartCount ?? 0);
const locale = computed(() => page.props.locale ?? 'en');
const menuItems = computed(() => [
    { label: translate(locale.value, 'home'), href: '/' },
    { label: translate(locale.value, 'shop'), href: '/shop' },
    { label: translate(locale.value, 'gallery'), href: '/#gallery' },
    { label: translate(locale.value, 'blog'), href: '/recipes' },
    { label: translate(locale.value, 'contact'), href: '/contact' },
]);
const menuOpen = ref(false);
const darkMode = ref(false);

function setTheme() {
    darkMode.value = localStorage.getItem('gj-theme') === 'dark';
    document.documentElement.classList.toggle('dark', darkMode.value);
}

function toggleTheme() {
    darkMode.value = !darkMode.value;
    document.documentElement.classList.toggle('dark', darkMode.value);
    localStorage.setItem('gj-theme', darkMode.value ? 'dark' : 'light');
}

function setLanguage(language) {
    router.post(`/language/${language}`, {}, { preserveScroll: true });
    menuOpen.value = false;
}

function isActive(path) {
    return path === '/' ? page.url === '/' : page.url.startsWith(path);
}

function revealOnScroll() {
    const elements = document.querySelectorAll('main > section, main article, main form, main aside, .reveal');

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
        elements.forEach((element) => element.classList.add('visible'));
        return null;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    elements.forEach((element) => {
        if (element.classList.contains('visible')) {
            return;
        }

        element.classList.add('reveal');
        observer.observe(element);
    });

    return observer;
}

let observer;
watch(() => page.url, async () => {
    await nextTick();
    observer?.disconnect();
    observer = revealOnScroll();
});
onMounted(() => {
    setTheme();
    observer = revealOnScroll();
});
onUnmounted(() => observer?.disconnect());
</script>

<template>
    <div :dir="locale === 'ur' ? 'rtl' : 'ltr'" :class="{ 'urdu': locale === 'ur' }" class="store-shell">
        <div class="bg-cane px-4 py-2 text-center text-xs font-semibold tracking-wide text-white">
            {{ translate(locale, 'top_banner') }}
            <span class="mx-2 opacity-50">·</span>
            <Link href="/track-order" class="underline underline-offset-2">{{ translate(locale, 'track_order') }}</Link>
        </div>
        <header class="site-header">
            <div class="container flex h-[78px] items-center justify-between gap-5">
                <Link href="/" class="flex shrink-0 items-center gap-3" aria-label="Golden Jaggery Co home">
                    <span class="brand-mark" aria-hidden="true">
                        <BrandMark />
                    </span>
                    <span>
                        <span class="serif block text-lg font-bold leading-tight">{{ brand.name }}</span>
                        <span class="brand-subtitle hidden text-[10px] tracking-wide sm:block">FRESH FROM OUR FAMILY TO YOURS</span>
                    </span>
                </Link>
                <nav class="hidden items-center gap-7 text-sm font-semibold lg:flex" aria-label="Main navigation">
                    <Link v-for="item in menuItems" :key="item.href" :href="item.href" class="nav-link" :class="{ 'nav-link-active': isActive(item.href) }">{{ item.label }}</Link>
                </nav>
                <div class="flex items-center gap-2">
                    <button class="header-action hidden rounded-full px-2 py-2 text-xs font-bold sm:block" type="button" :aria-label="translate(locale, 'language_toggle')" @click="setLanguage(locale === 'ur' ? 'en' : 'ur')">{{ locale === 'ur' ? 'EN' : 'اردو' }}</button>
                    <Link href="/login" class="header-action hidden h-10 w-10 place-items-center rounded-full hover:bg-black/5 sm:grid" :aria-label="translate(locale, 'account')">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="8" r="3.2" /><path d="M5.5 20a6.5 6.5 0 0 1 13 0" /></svg>
                    </Link>
                    <Link href="/shop" class="header-action hidden h-10 w-10 place-items-center rounded-full hover:bg-black/5 sm:grid" :aria-label="translate(locale, 'search_products')">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.4" /><path d="m16 16 4.2 4.2" /></svg>
                    </Link>
                    <button class="header-action grid h-10 w-10 place-items-center rounded-full hover:bg-black/5" type="button" :aria-label="darkMode ? translate(locale, 'light_mode') : translate(locale, 'dark_mode')" @click="toggleTheme">{{ darkMode ? '☀' : '☾' }}</button>
                    <Link href="/cart" class="header-action relative grid h-10 w-10 place-items-center rounded-full hover:bg-black/5" :aria-label="translate(locale, 'cart')">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 9h16l-1.6 10H5.6L4 9Z" /><path d="m8 9 4-5 4 5M8 13v3m4-3v3m4-3v3" /></svg>
                        <span v-if="cartCount" class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-amber px-1 text-[10px] font-bold text-white">{{ cartCount }}</span>
                    </Link>
                    <button class="header-menu grid h-10 w-10 place-items-center rounded-full hover:bg-black/5 lg:hidden" type="button" :aria-expanded="menuOpen" aria-label="Toggle navigation" @click="menuOpen = !menuOpen">{{ menuOpen ? '×' : '☰' }}</button>
                </div>
            </div>
            <nav v-if="menuOpen" class="absolute left-3 right-3 top-full flex flex-col gap-1 rounded-3xl border border-brown/10 bg-[var(--cream)] px-5 py-4 shadow-xl lg:hidden" aria-label="Mobile navigation">
                <Link v-for="(item, index) in menuItems" :key="item.href" :href="item.href" class="rounded-xl px-3 py-3 font-semibold" :style="{ animationDelay: `${index * 50}ms` }" @click="menuOpen = false">{{ item.label }}</Link>
                <Link href="/login" class="rounded-xl px-3 py-3 font-semibold" @click="menuOpen = false">{{ translate(locale, 'account') }}</Link>
                <button class="px-3 py-3 text-left font-semibold" type="button" @click="setLanguage(locale === 'ur' ? 'en' : 'ur')">{{ locale === 'ur' ? translate(locale, 'english') : translate(locale, 'urdu') }}</button>
            </nav>
        </header>

        <div v-if="page.props.flash?.success" class="container mt-4 rounded-2xl bg-[#e6eddc] px-4 py-3 text-sm text-cane" role="status">{{ page.props.flash.success }}</div>
        <div v-if="Object.keys(page.props.errors ?? {}).length" class="container mt-4 rounded-2xl bg-[#f7e6dc] px-4 py-3 text-sm text-[#7b3b22]" role="alert">
            <p v-for="(message, key) in page.props.errors" :key="key">{{ message }}</p>
        </div>

        <main class="site-main">
            <slot />
        </main>

        <footer class="site-footer">
            <div class="container grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="brand-mark footer-mark" aria-hidden="true">
                            <BrandMark />
                        </span>
                        <strong class="serif text-xl">{{ brand.name }}</strong>
                    </div>
                    <p class="max-w-sm text-sm leading-7 text-white/75">{{ brand.tagline }}</p>
                </div>
                <div>
                    <h2 class="serif mb-4 text-lg">Explore</h2>
                    <div class="flex flex-col gap-3 text-sm text-white/75"><Link href="/shop">Shop jaggery & gur</Link><Link href="/about">Our family story</Link><Link href="/wholesale">Wholesale</Link><Link href="/recipes">Recipes & journal</Link></div>
                </div>
                <div>
                    <h2 class="serif mb-4 text-lg">Here to help</h2>
                    <div class="flex flex-col gap-3 text-sm text-white/75"><Link href="/track-order">Track an order</Link><Link href="/contact">Contact us</Link><a :href="`mailto:${brand.email}`">{{ brand.email }}</a><span>{{ brand.location }}</span></div>
                </div>
            </div>
            <div class="border-t border-white/15 py-4 text-center text-xs text-white/60">© {{ new Date().getFullYear() }} {{ brand.name }} · Pure desi goodness, made with care.</div>
        </footer>

        <div class="mobile-bottom-bar">
            <Link href="/shop" class="button-primary">Order now <span aria-hidden="true">→</span></Link>
        </div>
        <a v-if="brand.whatsapp_enabled && brand.whatsapp_number" :href="`https://wa.me/${brand.whatsapp_number}`" class="fixed bottom-24 right-5 z-30 grid h-14 w-14 place-items-center rounded-full bg-[#1fbb65] text-2xl text-white shadow-xl md:bottom-7" aria-label="Chat with us on WhatsApp">◉</a>
    </div>
</template>
