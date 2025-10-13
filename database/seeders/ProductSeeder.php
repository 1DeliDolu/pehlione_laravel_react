<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private const PLACEHOLDER_IMAGE = 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAQAAAAAYLlVAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAGUlEQVR4nO3BAQEAAACCIP+vbkhAAQAAAAAAAAAA4HcBdAABFG1irQAAAABJRU5ErkJggg==';
    private const MIN_PRODUCTS_PER_CATEGORY = 3;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogue = [
            'white-goods' => [
                [
                    'name' => 'Aurora Front-Load Washer 9000',
                    'slug' => 'aurora-front-load-washer-9000',
                    'summary' => 'Energy-efficient 9kg washer with smart load balancing for modern households.',
                    'description' => 'Precision wash cycles paired with foam-sensor technology reduce detergent usage while the direct-drive motor cuts noise. Includes smart notifications and leak safeguards.',
                    'sku' => 'WHI-AUR-9000',
                    'size_profile' => 'appliance',
                    'available_sizes' => ['Standard'],
                    'material_profile' => 'hybrid',
                    'attribute_tags' => ['energy-efficient', 'smart-enabled', 'water-saving'],
                    'sustainability_notes' => [
                        'Uses 28% less water per cycle compared to legacy washers',
                        'Recycled steel housing and reusable filter cartridges',
                        'Eco-mode optimises detergent and temperature automatically',
                    ],
                    'care_instructions' => ['Wipe exterior with mild cleaner', 'Run drum clean programme monthly'],
                    'price' => 899.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 34,
                    'lead_time_days' => 5,
                    'energy_label' => 'A+++',
                    'metadata' => [
                        'intended_use' => 'Laundry rooms',
                        'package_contents' => 'Washer, inlet hoses, manual',
                    ],
                ],
                [
                    'name' => 'Polar Breeze No-Frost Refrigerator',
                    'slug' => 'polar-breeze-no-frost-refrigerator',
                    'summary' => 'Family-sized refrigerator with dual-zone cooling and active air purification.',
                    'description' => 'Keeps produce fresher for longer with humidity-managed drawers and a plasma filter that neutralises odours. Compatible with solar backup inverters.',
                    'sku' => 'WHI-POL-NF680',
                    'size_profile' => 'appliance',
                    'available_sizes' => ['Standard'],
                    'material_profile' => 'hybrid',
                    'attribute_tags' => ['eco-friendly', 'energy-efficient', 'smart-enabled'],
                    'sustainability_notes' => [
                        'Coolant uses low global warming potential refrigerant',
                        'Interior shelving crafted from 60% recycled glass',
                        'Night mode reduces power draw during off-peak hours',
                    ],
                    'care_instructions' => ['Vacuum condenser grills twice a year', 'Wipe seals with neutral detergent'],
                    'price' => 1249.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 21,
                    'lead_time_days' => 7,
                    'energy_label' => 'A++',
                    'metadata' => [
                        'capacity_litres' => 680,
                        'intended_use' => 'Kitchen refrigeration',
                    ],
                ],
            ],
            'consumer-electronics' => [
                [
                    'name' => 'Lumen Arc OLED TV 55"',
                    'slug' => 'lumen-arc-oled-tv-55',
                    'summary' => 'Ultra-thin OLED panel with adaptive brightness and recycled aluminium frame.',
                    'description' => 'Delivers cinema-grade contrast with AI tone mapping and low-latency gaming mode. Ships in fully recyclable packaging.',
                    'sku' => 'ELC-LUM-55',
                    'size_profile' => 'general',
                    'available_sizes' => ['55"'],
                    'material_profile' => 'recycled',
                    'attribute_tags' => ['eco-friendly', 'energy-efficient', 'smart-enabled'],
                    'sustainability_notes' => [
                        'Housing contains 48% post-consumer aluminium',
                        'Packaging avoids plastics and uses soy-based inks',
                        'Ambient eco-mode adjusts brightness to save energy',
                    ],
                    'care_instructions' => ['Dust with microfiber cloth', 'Avoid abrasive cleaners on screen'],
                    'price' => 1599.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 42,
                    'lead_time_days' => 3,
                    'energy_label' => 'A+',
                    'metadata' => [
                        'warranty_months' => 36,
                        'intended_use' => 'Home theatre',
                    ],
                ],
                [
                    'name' => 'PulseWave Smart Speaker Trio',
                    'slug' => 'pulsewave-smart-speaker-trio',
                    'summary' => 'Modular wireless speakers with natural fibre acoustic panels.',
                    'description' => 'Delivers room-aware sound staging and integrates voice assistants while the enclosures leverage bamboo composite for reduced resonance.',
                    'sku' => 'ELC-PUL-TRI',
                    'size_profile' => 'one_size',
                    'available_sizes' => ['One Size'],
                    'material_profile' => 'natural',
                    'attribute_tags' => ['eco-friendly', 'smart-enabled', 'modular'],
                    'sustainability_notes' => [
                        'Panels crafted from FSC-certified bamboo fibre',
                        'Electronics designed for easy disassembly and recycling',
                        'Low-VOC finishes throughout the enclosure',
                    ],
                    'care_instructions' => ['Dust with dry cloth', 'Keep away from direct moisture'],
                    'price' => 449.00,
                    'stock_status' => 'low_stock',
                    'stock_quantity' => 18,
                    'lead_time_days' => 10,
                    'energy_label' => null,
                    'metadata' => [
                        'bundle_items' => 3,
                        'intended_use' => 'Multi-room audio',
                    ],
                ],
            ],
            'garden-equipment' => [
                [
                    'name' => 'GreenPulse Solar Watering Hub',
                    'slug' => 'greenpulse-solar-watering-hub',
                    'summary' => 'Off-grid irrigation controller with weather-linked scheduling.',
                    'description' => 'Monitors soil moisture across up to eight zones and operates via solar power with rechargeable storage. Suitable for chemical-free gardens.',
                    'sku' => 'GAR-GRN-HUB',
                    'size_profile' => 'one_size',
                    'available_sizes' => ['One Size'],
                    'material_profile' => 'recycled',
                    'attribute_tags' => ['eco-friendly', 'recyclable', 'water-saving'],
                    'sustainability_notes' => [
                        'Solar cells contain 30% recycled silicon',
                        'Bioplastic enclosure from corn-based PLA',
                        'Rainwater mode prevents unnecessary watering',
                    ],
                    'care_instructions' => ['Clean panel with soft brush monthly', 'Store battery indoors below -10°C'],
                    'price' => 329.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 47,
                    'lead_time_days' => 4,
                    'energy_label' => null,
                    'metadata' => [
                        'zones_supported' => 8,
                        'intended_use' => 'Garden irrigation',
                    ],
                ],
                [
                    'name' => 'EcoTrim Quiet Hedge Cutter',
                    'slug' => 'ecotrim-quiet-hedge-cutter',
                    'summary' => 'Lightweight electric hedge trimmer with biodegradable lubricants.',
                    'description' => 'Delivers precise trimming using a brushless motor and vibration dampening grips. Ships with refillable plant-based lubricant cartridges.',
                    'sku' => 'GAR-ECO-TRM',
                    'size_profile' => 'one_size',
                    'available_sizes' => ['One Size'],
                    'material_profile' => 'hybrid',
                    'attribute_tags' => ['low-noise', 'eco-friendly', 'water-resistant'],
                    'sustainability_notes' => [
                        'Polymer casing blends recycled ABS with bamboo fibre',
                        'Blade treatments avoid heavy metals',
                        'Lubrication system uses biodegradable oils',
                    ],
                    'care_instructions' => ['Clean blades after each use', 'Store in dry location'],
                    'price' => 189.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 63,
                    'lead_time_days' => 3,
                    'energy_label' => null,
                    'metadata' => [
                        'blade_length_cm' => 60,
                        'intended_use' => 'Hedge maintenance',
                    ],
                ],
            ],
            'outdoor-furniture' => [
                [
                    'name' => 'TerraLounge Modular Sofa Set',
                    'slug' => 'terralounge-modular-sofa-set',
                    'summary' => 'Weather-resistant outdoor sofa with interchangeable cushion sets.',
                    'description' => 'Combines powder-coated aluminium with eucalyptus detailing and quick-dry foam. Modules reconfigure for lounge or dining layouts.',
                    'sku' => 'OUT-TER-SOF',
                    'size_profile' => 'modular',
                    'available_sizes' => ['Corner Module', 'Seat Module', 'Ottoman'],
                    'material_profile' => 'hybrid',
                    'attribute_tags' => ['modular', 'water-resistant', 'uv-stable'],
                    'sustainability_notes' => [
                        'Wood accents sourced from FSC-certified forests',
                        'Cushion fabrics woven from recycled PET bottles',
                        'Frames powder-coated without chromium additives',
                    ],
                    'care_instructions' => ['Cover during heavy rain', 'Wash cushion covers on gentle cycle'],
                    'price' => 2199.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 12,
                    'lead_time_days' => 14,
                    'energy_label' => null,
                    'metadata' => [
                        'configuration' => '3-seater + chaise + ottoman',
                        'intended_use' => 'Outdoor living areas',
                    ],
                ],
                [
                    'name' => 'ShadeWave Cantilever Parasol',
                    'slug' => 'shadewave-cantilever-parasol',
                    'summary' => 'Wind-tested parasol with recycled canvas canopy and LED edge lighting.',
                    'description' => 'Provides 3.5m coverage with adjustable tilt and integrated warm lighting for evening use. Base designed for rainwater ballast.',
                    'sku' => 'OUT-SHD-PAR',
                    'size_profile' => 'one_size',
                    'available_sizes' => ['One Size'],
                    'material_profile' => 'recycled',
                    'attribute_tags' => ['uv-stable', 'water-resistant', 'recyclable'],
                    'sustainability_notes' => [
                        'Canopy fabric produced from marine plastic recovery programme',
                        'LED strips are low-voltage and replaceable',
                        'Aluminium frame manufactured with 70% recycled content',
                    ],
                    'care_instructions' => ['Rinse canopy monthly', 'Lower parasol during storms'],
                    'price' => 699.00,
                    'stock_status' => 'low_stock',
                    'stock_quantity' => 9,
                    'lead_time_days' => 6,
                    'energy_label' => null,
                    'metadata' => [
                        'coverage_diameter_m' => 3.5,
                        'intended_use' => 'Patio shading',
                    ],
                ],
            ],
            'home-furniture' => [
                [
                    'name' => 'Nordrum Solid Oak Dining Table',
                    'slug' => 'nordrum-solid-oak-dining-table',
                    'summary' => 'Extendable dining table featuring natural oil finish and concealed cable channel.',
                    'description' => 'Seats eight with brass-free hardware and replaceable leaf panels. Cable channel keeps lighting cords hidden for clean setups.',
                    'sku' => 'FUR-NOR-TBL',
                    'size_profile' => 'general',
                    'available_sizes' => ['Standard', 'Extended'],
                    'material_profile' => 'natural',
                    'attribute_tags' => ['eco-friendly', 'chemical-free', 'modular'],
                    'sustainability_notes' => [
                        'Sourced from responsibly managed European oak forests',
                        'Finished with plant-based oils free from solvents',
                        'Designed for flat-pack shipping to reduce emissions',
                    ],
                    'care_instructions' => ['Re-oil annually', 'Wipe spills promptly'],
                    'price' => 1399.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 17,
                    'lead_time_days' => 21,
                    'energy_label' => null,
                    'metadata' => [
                        'seating_capacity' => 8,
                        'intended_use' => 'Dining spaces',
                    ],
                ],
                [
                    'name' => 'Calma Cloud Sofa',
                    'slug' => 'calma-cloud-sofa',
                    'summary' => 'Low-profile sofa with removable merino covers and recycled foam core.',
                    'description' => 'Modular chaise configuration featuring breathable natural fabrics and tool-free assembly. Cushion cores are 65% recycled foam.',
                    'sku' => 'FUR-CAL-SOF',
                    'size_profile' => 'modular',
                    'available_sizes' => ['2-Seater', 'Chaise Module'],
                    'material_profile' => 'hybrid',
                    'attribute_tags' => ['eco-friendly', 'modular', 'chemical-free'],
                    'sustainability_notes' => [
                        'Covers woven from untreated merino wool',
                        'Frame uses recycled steel and FSC plywood',
                        'Modules shipped in recyclable corrugate only',
                    ],
                    'care_instructions' => ['Vacuum weekly', 'Dry clean covers as needed'],
                    'price' => 1899.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 14,
                    'lead_time_days' => 18,
                    'energy_label' => null,
                    'metadata' => [
                        'configuration' => '2-seater + chaise',
                        'intended_use' => 'Living rooms',
                    ],
                ],
            ],
            'kitchen-dining' => [
                [
                    'name' => 'HarvestStone Ceramic Cookware Set',
                    'slug' => 'harveststone-ceramic-cookware-set',
                    'summary' => 'Non-toxic ceramic cookware with detachable handles for oven-to-table serving.',
                    'description' => 'Features mineral-based ceramic coating free from PFAS and cadmium. Nesting design saves space; handles are recyclable aluminium.',
                    'sku' => 'KCH-HAR-SET',
                    'size_profile' => 'set',
                    'available_sizes' => ['2L Pot', '4L Pot', '24cm Pan'],
                    'material_profile' => 'natural',
                    'attribute_tags' => ['chemical-free', 'recyclable', 'heat-efficient'],
                    'sustainability_notes' => [
                        'Ceramic glaze uses lead-free formulation',
                        'Handles detach for recycling at end-of-life',
                        'Packaging is molded fibre with zero plastics',
                    ],
                    'care_instructions' => ['Hand wash recommended', 'Avoid metal utensils'],
                    'price' => 369.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 58,
                    'lead_time_days' => 2,
                    'energy_label' => null,
                    'metadata' => [
                        'pieces' => 6,
                        'intended_use' => 'Home cooking',
                    ],
                ],
                [
                    'name' => 'FlowForm Water Carafe Set',
                    'slug' => 'flowform-water-carafe-set',
                    'summary' => 'Hand-blown glass carafes with charcoal filtration sleeves.',
                    'description' => 'Infuses water with natural minerals through reusable charcoal sticks while the glass is heat-treated for durability. Sleeves are organic cotton.',
                    'sku' => 'KCH-FLW-SET',
                    'size_profile' => 'set',
                    'available_sizes' => ['1L Carafe', '0.5L Carafe'],
                    'material_profile' => 'natural',
                    'attribute_tags' => ['chemical-free', 'eco-friendly', 'reusable'],
                    'sustainability_notes' => [
                        'Charcoal filters sourced from sustainable oak offcuts',
                        'Sleeves dyed with low-impact plant pigments',
                        'Glassware manufactured in zero-waste facility',
                    ],
                    'care_instructions' => ['Hand wash glass', 'Boil charcoal sticks quarterly'],
                    'price' => 149.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 73,
                    'lead_time_days' => 3,
                    'energy_label' => null,
                    'metadata' => [
                        'set_size' => 2,
                        'intended_use' => 'Dining & hydration',
                    ],
                ],
            ],
            'smart-home-iot' => [
                [
                    'name' => 'Nestlink Climate Sensor Kit',
                    'slug' => 'nestlink-climate-sensor-kit',
                    'summary' => 'Multi-room climate sensors with recycled polymer casings and Matter support.',
                    'description' => 'Tracks temperature, humidity, and air quality while interfacing with major home automation hubs. Includes automations for energy saving.',
                    'sku' => 'IOT-NST-KIT',
                    'size_profile' => 'set',
                    'available_sizes' => ['Sensor Trio'],
                    'material_profile' => 'recycled',
                    'attribute_tags' => ['smart-enabled', 'energy-efficient', 'modular'],
                    'sustainability_notes' => [
                        'Casings molded from recycled ABS',
                        'Firmware supports long-term software updates to extend life',
                        'Packaging is 100% paper-based with plant inks',
                    ],
                    'care_instructions' => ['Dust monthly', 'Avoid direct water exposure'],
                    'price' => 279.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 61,
                    'lead_time_days' => 4,
                    'energy_label' => null,
                    'metadata' => [
                        'connectivity' => ['Thread', 'Wi-Fi'],
                        'intended_use' => 'Home automation',
                    ],
                ],
                [
                    'name' => 'SafeGlow Smart Lighting Nodes',
                    'slug' => 'safeglow-smart-lighting-nodes',
                    'summary' => 'Low-voltage smart lighting nodes with biodegradable diffusers.',
                    'description' => 'Magnetically mounts under cabinets or shelving and provides tunable white and coloured scenes. Diffusers are plant-based biopolymer.',
                    'sku' => 'IOT-SAF-LIT',
                    'size_profile' => 'set',
                    'available_sizes' => ['3-Pack', '6-Pack'],
                    'material_profile' => 'hybrid',
                    'attribute_tags' => ['smart-enabled', 'energy-efficient', 'modular'],
                    'sustainability_notes' => [
                        'LED arrays rated for 50,000 hours reducing replacements',
                        'Diffusers compostable under industrial conditions',
                        'Power supplies meet Level VI efficiency standards',
                    ],
                    'care_instructions' => ['Wipe diffusers with dry cloth', 'Keep drivers ventilated'],
                    'price' => 229.00,
                    'stock_status' => 'low_stock',
                    'stock_quantity' => 19,
                    'lead_time_days' => 5,
                    'energy_label' => 'A',
                    'metadata' => [
                        'bundle_items' => [3, 6],
                        'intended_use' => 'Accent lighting',
                    ],
                ],
            ],
            'home-office-essentials' => [
                [
                    'name' => 'Elevate Sit-Stand Desk',
                    'slug' => 'elevate-sit-stand-desk',
                    'summary' => 'Programmable sit-stand desk with bamboo surface and silent motor.',
                    'description' => 'Features cable routing spine, wireless charging pad, and anti-collision sensors. Surface finished with water-based sealant.',
                    'sku' => 'OFF-ELV-DSK',
                    'size_profile' => 'general',
                    'available_sizes' => ['120cm', '150cm'],
                    'material_profile' => 'natural',
                    'attribute_tags' => ['eco-friendly', 'smart-enabled', 'modular'],
                    'sustainability_notes' => [
                        'Bamboo sourced from fast-regrowth plantations',
                        'Frame coated using low-emission powder paints',
                        'Electronics designed for component-level repair',
                    ],
                    'care_instructions' => ['Wipe surface weekly', 'Recalibrate lift every 6 months'],
                    'price' => 749.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 28,
                    'lead_time_days' => 9,
                    'energy_label' => null,
                    'metadata' => [
                        'height_range_cm' => '65-125',
                        'intended_use' => 'Home office desks',
                    ],
                ],
                [
                    'name' => 'Breathe Comfort Ergonomic Chair',
                    'slug' => 'breathe-comfort-ergonomic-chair',
                    'summary' => 'Mesh-backed chair with recycled nylon frame and modular lumbar system.',
                    'description' => 'Supports healthy posture with adjustable tension zones and upholstered arm pads. Mesh fabric is derived from ocean-bound plastics.',
                    'sku' => 'OFF-BRE-CHR',
                    'size_profile' => 'standard_apparel',
                    'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'material_profile' => 'recycled',
                    'attribute_tags' => ['eco-friendly', 'modular', 'chemical-free'],
                    'sustainability_notes' => [
                        'Seat foam utilises bio-based polyols',
                        'Packaging employs recycled cardboard honeycomb',
                        'Designed for easy part replacement to extend lifecycle',
                    ],
                    'care_instructions' => ['Vacuum mesh monthly', 'Spot clean arm pads with mild soap'],
                    'price' => 529.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 45,
                    'lead_time_days' => 7,
                    'energy_label' => null,
                    'metadata' => [
                        'recommended_hours_per_day' => 10,
                        'intended_use' => 'Desk seating',
                    ],
                ],
            ],
            'lighting-fixtures' => [
                [
                    'name' => 'VerdeGlow Pendant Trio',
                    'slug' => 'verdeglow-pendant-trio',
                    'summary' => 'Handcrafted pendants with recycled glass shades and dimmable LED cores.',
                    'description' => 'Ideal for kitchen islands, featuring adjustable braided cables and warm-to-cool lighting options controlled via app or wall dimmer.',
                    'sku' => 'LGT-VER-PEN',
                    'size_profile' => 'set',
                    'available_sizes' => ['3-Pendant Set'],
                    'material_profile' => 'recycled',
                    'attribute_tags' => ['eco-friendly', 'energy-efficient', 'smart-enabled'],
                    'sustainability_notes' => [
                        'Glass shades hand-blown from post-consumer bottles',
                        'LED modules user-replaceable to extend fixture life',
                        'Braided textile cable uses organic cotton fibres',
                    ],
                    'care_instructions' => ['Dust shades weekly', 'Use non-abrasive cleaner on glass'],
                    'price' => 389.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 31,
                    'lead_time_days' => 6,
                    'energy_label' => 'A+',
                    'metadata' => [
                        'ceiling_drop_cm' => 120,
                        'intended_use' => 'Kitchen & dining illumination',
                    ],
                ],
                [
                    'name' => 'Biolume Floor Lamp',
                    'slug' => 'biolume-floor-lamp',
                    'summary' => 'Adaptive standing lamp with bio-resin diffuser and motion-aware brightness.',
                    'description' => 'Shifts colour temperature through the day, supports manual overrides, and utilises bio-resin diffusers derived from sugarcane.',
                    'sku' => 'LGT-BIO-LMP',
                    'size_profile' => 'one_size',
                    'available_sizes' => ['One Size'],
                    'material_profile' => 'hybrid',
                    'attribute_tags' => ['eco-friendly', 'energy-efficient', 'smart-enabled'],
                    'sustainability_notes' => [
                        'Bio-resin body is recyclable through partner programme',
                        'Adaptive lighting reduces energy consumption by up to 35%',
                        'Packaging filler is compostable corn starch foam',
                    ],
                    'care_instructions' => ['Wipe with dry cloth', 'Do not submerge base'],
                    'price' => 269.00,
                    'stock_status' => 'in_stock',
                    'stock_quantity' => 37,
                    'lead_time_days' => 4,
                    'energy_label' => 'A',
                    'metadata' => [
                        'intended_use' => 'Living room & reading nooks',
                        'sensors' => ['motion', 'ambient-light'],
                    ],
                ],
            ],
        ];

        $categories = Category::query()
            ->whereIn('slug', array_keys($catalogue))
            ->get()
            ->keyBy('slug');

        foreach ($catalogue as $slug => $products) {
            $category = $categories->get($slug);

            if (! $category) {
                continue;
            }

            foreach ($products as $productData) {
                $payload = array_merge($productData, [
                    'category_id' => $category->id,
                    'slug' => $productData['slug'] ?? Str::slug($productData['name']),
                ]);

                $product = Product::updateOrCreate(
                    ['slug' => $payload['slug']],
                    $payload
                );

                $this->assignImages($product);
            }

            $category->unsetRelation('products');
            $category->load('products');

            $missingProducts = max(0, self::MIN_PRODUCTS_PER_CATEGORY - $category->products->count());

            if ($missingProducts > 0) {
                Product::factory()
                    ->count($missingProducts)
                    ->for($category)
                    ->create()
                    ->each(fn (Product $product) => $this->assignImages($product));
            }
        }
    }

    private function assignImages(Product $product, int $count = 4): void
    {
        $slug = $product->slug ?: Str::slug($product->name);
        $images = $this->provisionImages($slug, $count);

        $product->forceFill([
            'slug' => $slug,
            'images' => $images,
        ])->save();
    }

    /**
     * @return array<int, string>
     */
    private function provisionImages(string $slug, int $count = 4): array
    {
        $directory = public_path('products/'.$slug);
        File::ensureDirectoryExists($directory);

        $paths = [];
        $binary = base64_decode(self::PLACEHOLDER_IMAGE);

        for ($i = 1; $i <= $count; $i++) {
            $filename = "image-{$i}.png";
            $fullPath = $directory.DIRECTORY_SEPARATOR.$filename;

            if (! File::exists($fullPath)) {
                File::put($fullPath, $binary);
            }

            $paths[] = '/products/'.$slug.'/'.$filename;
        }

        return $paths;
    }
}
