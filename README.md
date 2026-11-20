# Golden Jaggery Co

A Laravel e-commerce starter for family-made gur, jaggery and shakkar. The storefront uses Laravel 13, Inertia.js, Vue 3, Tailwind CSS and Three.js; the password-protected `/admin` panel is built with Filament.

## Requirements

- PHP 8.3+ with `intl`, `mbstring`, `pdo_mysql` (or `pdo_sqlite` for local development), `fileinfo`, `openssl` and `xml`
- Composer 2
- Node.js 20.19+ or 22.12+ and npm
- MySQL 8+ for production (SQLite is the default for local development)

> Filament requires PHP's `intl` extension. Enable it in the active PHP installation before using the admin panel or running `composer install` on a new machine.

## Local setup

1. Copy `.env.example` to `.env`.
2. Install dependencies and build the frontend:

   ```powershell
   composer install
   npm install
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   php artisan serve
   ```

3. Open `http://127.0.0.1:8000`. Start Vite separately with `npm run dev` while developing.php artisan db:seed
4. Configure admin credentials in `.env` (`ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`) and run `` to create the first administrator. The admin signs in at `/admin`.
5. In Filament, open **Store operations → Shipping rates**. The seeded rates are examples and are inactive. Replace them with contracted shipping charges and activate the rates before accepting orders. An order cannot be placed when its destination has no active rate.

## Business details and catalog

- Set `BRAND_LOCATION`, `BRAND_PHONE`, `BRAND_EMAIL` and `BRAND_WHATSAPP_NUMBER` in `.env`. Use an international phone format for WhatsApp, digits only (for example `923001234567`).
- Edit public contact details, homepage copy, brand name and tagline in **Content → Site settings**. Update the brand colors in `config/golden-jaggery.php` and `resources/css/app.css`.
- Replace sample prices, stock counts, photos and product text in **Catalog → Products**. A product’s variant relation contains SKU, pack size, PKR price, wholesale price and stock.
- The seed catalog uses replaceable local SVG illustrations in `public/images/`. Replace these with product photos you own and host on your own storage/CDN before launch.
- The seed catalog's prices, inventory, blog articles and inactive shipping amounts are demonstration data, not business quotes. Verify all pricing, product ingredients, allergen details, claims, availability and destination rates before launch.
- Add country-wise international rates and Pakistan city or country-wide rates in the Filament **Shipping rates** resource. Activate only destinations you can serve.
- Create discount codes in **Store operations → Discount codes**. Create and publish verified testimonials and recipe content in **Content**.

## Checkout and operations

- Guests can place orders without creating an account. Orders are persisted transactionally; in-stock variants are locked and deducted only after a successful order, while pre-orders do not decrement current stock.
- Cash on delivery is available for Pakistan. Bank transfer is available for domestic and international orders. The application does not collect card data; integrate JazzCash, Easypaisa, Stripe or PayPal only after merchant credentials and payment verification are ready.
- Add real SMTP settings and run a queue worker for order confirmation email:

  ```powershell
  php artisan queue:work
  ```

- Use the order status editor in Filament to confirm, pack, ship, deliver or cancel orders. Add a carrier tracking number for shipped orders. Packing slips can be printed from each order; use **Export CSV** on the order list for a spreadsheet export.
- Order tracking requires both the order number and the checkout phone number.
- The product gallery has a procedural Three.js gur/shakkar viewer. It is an illustrative 3D scene, not a scan of the physical batch.

## Production deployment (cPanel or VPS)

1. Provision PHP 8.3+ with the extensions above, MySQL, Composer, Node/npm for the build, HTTPS and a writable Laravel `storage/` and `bootstrap/cache/`.
2. Set production `.env` values securely: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`, MySQL credentials, a unique `APP_KEY`, SMTP credentials, queue settings and the correct `BRAND_*` / `ADMIN_*` values. Never commit `.env` or expose `ADMIN_PASSWORD`.
3. Install production PHP dependencies and build assets:

   ```sh
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   php artisan optimize
   ```

   If building on another host, deploy the generated `public/build/` directory with the release.
4. Point the domain document root to Laravel's `public/` directory. Do not expose the repository root, `.env`, `vendor/` or database files through the web server.
5. Configure a persistent queue worker (`php artisan queue:work --sleep=3 --tries=3`) with Supervisor on VPS or the host's queue manager. For cPanel, use its terminal/cron/process manager as available. Configure `QUEUE_CONNECTION=database` or a managed Redis queue.
6. Configure database backups, log monitoring, TLS renewal, rate limits, email delivery, a real payment process, and tested shipping fees. Keep `APP_DEBUG=false` in production.
7. Test a complete order, stock reservation, cancellation policy, tracking, queued email, CSV export, packing slip, each enabled shipping zone and both language directions before opening sales.

## Validation

```powershell
php artisan test
composer validate --no-check-publish
npm audit
npm run build
```
