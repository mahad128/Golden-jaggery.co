<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingRate;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StorefrontSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'slug' => 'family-gur-block',
                'name' => 'Family Gur Block',
                'name_ur' => 'دیسی گڑ',
                'type' => 'gur block',
                'short_description' => 'A rich, mellow gur block, slowly cooked from fresh sugarcane juice.',
                'description' => 'Our traditional gur block is made in small family batches from sugarcane grown close to home. Enjoy its deep caramel notes in tea, desserts or a simple bite after a meal.',
                'description_ur' => 'تازہ گنے کے رس سے تیار کیا گیا روایتی دیسی گڑ۔',
                'benefits' => ['A naturally unrefined cane sweetener', 'Small-batch, family-made', 'No artificial colour or flavour added'],
                'how_to_use' => ['Stir into chai or warm milk', 'Grate over porridge or yogurt', 'Use in traditional sweets and sauces'],
                'images' => ['/images/gur-block.svg'],
                'status' => 'in_stock',
                'is_featured' => true,
                'seo_description' => 'Shop pure desi jaggery gur block in 500 g, 1 kg and 5 kg packs, made fresh from our family sugarcane.',
                'variants' => [['500 g', 500, 280, 220], ['1 kg', 1000, 520, 410], ['5 kg', 5000, 2450, 2050]],
            ],
            [
                'slug' => 'golden-gur-powder',
                'name' => 'Golden Gur Powder',
                'name_ur' => 'گڑ پاؤڈر',
                'type' => 'gur powder',
                'short_description' => 'Easy-to-scoop gur powder for everyday tea, baking and home cooking.',
                'description' => 'All the warmth of classic gur in a convenient, easy-to-measure powder. Packed fresh to bring a round, caramel sweetness to everyday favourites.',
                'description_ur' => 'چائے، مٹھائی اور روزمرہ کھانوں کے لیے آسان گڑ پاؤڈر۔',
                'benefits' => ['Convenient for everyday recipes', 'Traditional unrefined sweetness', 'Packed in a resealable pouch'],
                'how_to_use' => ['Sweeten tea, coffee or milk', 'Blend into lassi and smoothies', 'Replace refined sugar in home recipes'],
                'images' => ['/images/gur-powder.svg'],
                'status' => 'in_stock',
                'is_featured' => true,
                'seo_description' => 'Pure gur powder and jaggery powder for chai, baking and home cooking, available in family-friendly sizes.',
                'variants' => [['500 g', 500, 320, 250], ['1 kg', 1000, 600, 480], ['5 kg', 5000, 2800, 2350]],
            ],
            [
                'slug' => 'cane-shakkar',
                'name' => 'Cane Shakkar',
                'name_ur' => 'گنے کی شکر',
                'type' => 'shakkar',
                'short_description' => 'Coarse, golden shakkar with the gentle character of unrefined cane.',
                'description' => 'Our family shakkar is made from sugarcane juice and kept beautifully simple. Sprinkle over breakfast or use in traditional recipes that call for a mellow cane sweetness.',
                'description_ur' => 'گنے کے رس سے تیار کردہ سادہ اور خوش ذائقہ شکر۔',
                'benefits' => ['Unrefined cane sweetness', 'Coarse texture for sprinkling and cooking', 'Made in seasonal batches'],
                'how_to_use' => ['Sprinkle over cereal or fruit', 'Add to tea and warm drinks', 'Use in halwa and baked treats'],
                'images' => ['/images/shakkar.svg'],
                'status' => 'in_stock',
                'is_featured' => true,
                'seo_description' => 'Buy family-made shakkar, an unrefined cane sugar with a gentle golden flavour.',
                'variants' => [['500 g', 500, 300, 235], ['1 kg', 1000, 560, 450], ['5 kg', 5000, 2600, 2200]],
            ],
            [
                'slug' => 'ginger-gur',
                'name' => 'Ginger Gur',
                'name_ur' => 'ادرک والا گڑ',
                'type' => 'flavoured gur',
                'short_description' => 'A warming ginger twist on our slow-cooked, family-made gur.',
                'description' => 'A small-batch seasonal favourite with a gentle ginger warmth. Lovely in winter tea and warm milk. Ingredient details and allergen information should be confirmed for each batch before sale.',
                'description_ur' => 'ادرک کی خوشبو کے ساتھ موسمی گڑ۔',
                'benefits' => ['Warm ginger flavour', 'Crafted in small seasonal batches', 'A cosy addition to hot drinks'],
                'how_to_use' => ['Add a little to winter chai', 'Stir into warm milk', 'Enjoy in small pieces after meals'],
                'images' => ['/images/ginger-gur.svg'],
                'status' => 'pre_order',
                'is_featured' => true,
                'seo_description' => 'Seasonal ginger gur jaggery, available to pre-order in limited small batches.',
                'variants' => [['500 g', 500, 390, 320], ['1 kg', 1000, 740, 620], ['5 kg', 5000, 3500, 3000]],
            ],
            [
                'slug' => 'saunf-gur',
                'name' => 'Saunf Gur',
                'name_ur' => 'سونف والا گڑ',
                'type' => 'flavoured gur',
                'short_description' => 'A fragrant fennel gur batch, returning when the new season is ready.',
                'description' => 'This fragrant family favourite is made in seasonal batches. It is currently out of season; check back or ask us when the next batch will be ready.',
                'description_ur' => 'سونف کی خوشبو والا موسمی گڑ؛ اگلی فصل کے بعد دستیاب ہوگا۔',
                'benefits' => ['Fragrant fennel flavour', 'Seasonal family-made batch', 'Made with traditional care'],
                'how_to_use' => ['Enjoy a small piece after a meal', 'Pair with tea', 'Use in seasonal sweets'],
                'images' => ['/images/gur-block.svg'],
                'status' => 'out_of_season',
                'is_featured' => false,
                'seo_description' => 'Fennel saunf gur jaggery is seasonal and currently out of season.',
                'variants' => [['500 g', 500, 370, 300], ['1 kg', 1000, 700, 580], ['5 kg', 5000, 3300, 2850]],
            ],
        ];

        foreach ($products as $data) {
            $variants = $data['variants'];
            unset($data['variants']);
            $product = Product::query()->updateOrCreate(['slug' => $data['slug']], $data);

            foreach ($variants as [$size, $weight, $price, $wholesalePrice]) {
                ProductVariant::query()->updateOrCreate(
                    ['sku' => Str::upper(Str::slug($product->slug.'-'.$size))],
                    [
                        'product_id' => $product->id,
                        'size' => $size,
                        'weight_grams' => $weight,
                        'price' => $price,
                        'wholesale_price' => $wholesalePrice,
                        'stock' => $product->status === 'in_stock' ? 100 : 0,
                        'is_active' => true,
                    ],
                );
            }
        }

        foreach ([
            ['title' => 'A cosy gur chai for slow mornings', 'slug' => 'gur-chai-slow-mornings', 'category' => 'Recipes', 'excerpt' => 'A simple way to bring the deep, mellow sweetness of gur to your everyday cup of chai.', 'body' => 'Warm your favourite milk or water with tea leaves and a slice of fresh ginger. Once brewed, turn off the heat and stir in a little gur to taste. Start small, then adjust to your cup. Add cardamom if you like, strain and enjoy warm.', 'image' => '/images/recipe-tea.svg'],
            ['title' => 'Three easy ways to use gur powder', 'slug' => 'easy-ways-to-use-gur-powder', 'category' => 'Guides', 'excerpt' => 'A few no-fuss ideas for making gur powder part of your everyday kitchen.', 'body' => 'Stir gur powder into chai, sprinkle it over porridge, or use it to sweeten a homemade lassi. In baking, try it in place of some of the sugar and adjust the liquid to suit your recipe. Every gur batch can vary a little in flavour and sweetness.', 'image' => '/images/gur-powder.svg'],
            ['title' => 'How we make a fresh gur batch', 'slug' => 'how-a-fresh-gur-batch-is-made', 'category' => 'Farm stories', 'excerpt' => 'From fresh-cut cane to the golden block packed for your family table.', 'body' => 'We harvest ripe sugarcane, press it while fresh, filter the juice and slowly cook it in traditional pans. When the texture is right, we cool, shape and pack the gur. The exact timing follows the season and the batch.', 'image' => '/images/sugarcane-field.svg'],
        ] as $post) {
            BlogPost::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [...$post, 'is_published' => true, 'published_at' => now()->subDays(rand(1, 21))],
            );
        }

        foreach ([
            'brand_name' => ['Brand name', config('golden-jaggery.name')],
            'tagline' => ['Tagline', config('golden-jaggery.tagline')],
            'location' => ['Production location', config('golden-jaggery.location')],
            'phone' => ['Phone / WhatsApp display', config('golden-jaggery.phone')],
            'email' => ['Business email', config('golden-jaggery.email')],
            'whatsapp_number' => ['WhatsApp number (international digits only)', config('golden-jaggery.whatsapp_number')],
            'whatsapp_enabled' => ['Enable WhatsApp button (true / false)', config('golden-jaggery.whatsapp_enabled') ? 'true' : 'false'],
            'bank_transfer_instructions' => ['Bank transfer instructions', config('golden-jaggery.bank_transfer_instructions')],
            'hero_title' => ['Home hero heading', 'Fresh and Pure Jaggery'],
            'hero_subtitle' => ['Home hero description', 'Pure. Natural. Chemical-free jaggery that nourishes your body.'],
            'story_title' => ['Home process heading', 'Good things take a little time.'],
            'story_text' => ['Home process introduction', 'Every golden batch follows the same honest journey—no shortcuts, just our family’s hands-on care.'],
        ] as $key => [$label, $value]) {
            SiteSetting::query()->firstOrCreate(['key' => $key], ['label' => $label, 'value' => $value]);
        }

        foreach ([
            ['scope' => 'city', 'city' => null, 'country' => 'Pakistan', 'amount' => 250],
            ['scope' => 'city', 'city' => 'Karachi', 'country' => 'Pakistan', 'amount' => 250],
            ['scope' => 'city', 'city' => 'Lahore', 'country' => 'Pakistan', 'amount' => 250],
            ['scope' => 'city', 'city' => 'Islamabad', 'country' => 'Pakistan', 'amount' => 300],
            ['scope' => 'country', 'city' => null, 'country' => 'United Arab Emirates', 'amount' => 4500],
            ['scope' => 'country', 'city' => null, 'country' => 'United Kingdom', 'amount' => 9500],
            ['scope' => 'country', 'city' => null, 'country' => 'United States', 'amount' => 14000],
        ] as $rate) {
            ShippingRate::query()->firstOrCreate(
                ['scope' => $rate['scope'], 'city' => $rate['city'], 'country' => $rate['country']],
                ['amount' => $rate['amount'], 'is_active' => false],
            );
        }
    }
}
