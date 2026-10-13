<?php

namespace App\Import;

/**
 * Ana sayfa ve araç sayfalarındaki widget'ların İngilizce metinleri.
 *
 * Türkçe metinler widget'ların kendi varsayılanlarıdır; burada yalnızca
 * İngilizce ağaçta onların yerine geçecek ayarlar durur. Kurulumdan sonra
 * metinler panelde her dilin sayfasında ayrı ayrı düzenlenir.
 */
class HomeCopy
{
    public static function english(): array
    {
        return [
            'hero_gauge' => [
                'eyebrow' => 'Torque · Hydraulics · Bolting Solutions',
                'line1' => 'Every bolt.',
                'line2' => 'Right torque.',
                'line3' => 'Every time.',
                'lead' => 'Hydraulic torque wrenches, 700 bar cylinders and pumps. Delivery from stock, service and calibration in Türkiye.',
                'primary_text' => 'Explore products',
                'secondary_text' => 'Request a quote',
                'specs' => [
                    ['value' => '26,664', 'label' => 'N·m max.'],
                    ['value' => '±3%', 'label' => 'Accuracy'],
                    ['value' => '700', 'label' => 'Bar system'],
                ],
                'tag_top' => 'M64 · Bolt',
                'tag_bottom' => '±3% · Repeatability',
                'hint' => 'Scroll to tighten',
            ],
            'category_tiles' => [
                'eyebrow' => '01 — Product groups',
                'title' => "Three disciplines.\nOne supplier.",
                'text' => 'From torque tools to 700 bar hydraulic systems, everything your field crew needs in a single quote.',
            ],
            'product_grid' => [
                'eyebrow' => '02 — Featured',
                'title' => "Add to quote.\nWe bring the price.",
                'text' => 'Every project is different, so prices are quoted. Add products to your quote, set quantities and we will send a tailored offer within 24 hours.',
                'button_text' => 'View all products',
            ],
            'steps' => [
                'eyebrow' => '03 — How quoting works',
                'title' => "Four steps\nto the job site.",
                'items' => [
                    ['title' => 'Select', 'text' => 'Add the products you need to your quote and enter quantities.'],
                    ['title' => 'Send', 'text' => 'Leave your company and contact details. No account needed.'],
                    ['title' => 'Get a quote', 'text' => 'Our engineer reviews your application and sends a price within 24 hours.'],
                    ['title' => 'On site', 'text' => 'Fast dispatch from stock; on-site commissioning and operator training if needed.'],
                ],
            ],
            'service_rail' => [
                'eyebrow' => '04 — Services',
                'title' => 'More than tools.',
                'items' => [
                    ['title' => 'Torque wrench rental', 'tag' => 'Daily · Weekly', 'text' => 'Calibrated hydraulic torque kits for shutdowns and construction sites, supplied with pump and hoses.'],
                    ['title' => 'Calibration and repair', 'tag' => 'ISO 6789', 'text' => 'Traceable calibration certificates for torque tools of every brand, with a reminder every 12 months.'],
                    ['title' => 'Building and bridge lifting', 'tag' => 'Synchronous', 'text' => 'Millimetre-precise lifting and bearing replacement with multi-point synchronous hydraulic systems.'],
                    ['title' => 'On-site bolting', 'tag' => 'In the field', 'text' => 'Our crew tightens flange and tower joints to procedure and reports every bolt.'],
                    ['title' => 'Operator training', 'tag' => 'Hands-on', 'text' => 'Field training on safe use and bolt tightening procedures.'],
                ],
            ],
            'stat_band' => [
                'items' => [
                    ['value' => 24, 'suffix' => 'h', 'label' => 'To answer every quote request'],
                    ['value' => 700, 'suffix' => '', 'label' => 'Bar working pressure across the hydraulic line'],
                    ['value' => 12, 'suffix' => 'mo', 'label' => 'Warranty; service and spare parts in Türkiye'],
                    ['value' => 22, 'suffix' => '+', 'label' => 'Models available from stock'],
                ],
            ],
            'industry_tabs' => [
                'eyebrow' => '05 — Industries',
                'title' => "Wherever a joint\nmust hold.",
                'items' => [
                    ['title' => 'Wind energy', 'text' => 'High cycle counts and low operator fatigue on tower, hub and nacelle bolts.', 'points' => "Battery torque wrench\nBolt tensioning kit\nTower flange sockets\nCalibration certificate"],
                    ['title' => 'Petrochemical and refining', 'text' => 'Cross-pattern tightening for leak-free pipeline flanges, flange spreading and hydrostatic testing.', 'points' => "Low profile torque wrench\nWedge flange spreader\nFlange alignment tool\nHydrostatic test pump"],
                    ['title' => 'Construction and bridges', 'text' => 'Synchronous lifting, bearing replacement and heavy load positioning.', 'points' => "Double-acting cylinders\nElectric pump\nManifold and gauge\nSynchronous lifting service"],
                    ['title' => 'Mining', 'text' => 'Fast field response on crushers, mills and heavy machinery.', 'points' => "Nut splitter\nHydraulic puller\nHeavy duty sockets\nBottle jack"],
                    ['title' => 'Marine and ports', 'text' => 'Controlled torque on shaft couplings, propellers and crane connections.', 'points' => "Square drive torque wrench\nTorque multiplier\nHand pump\nOn-site bolting service"],
                ],
            ],
            'trust_grid' => [
                'eyebrow' => '06 — Why Rimpy',
                'title' => "Proof\non paper.",
                'text' => 'As a new brand we build trust with documents, not promises. Every product\'s certificates are on its page, ready to download.',
                'items' => [
                    ['icon' => 'badge-check', 'title' => 'Test certificate', 'text' => 'Every cylinder and pump is pressure tested before dispatch; the report ships with the product.'],
                    ['icon' => 'award', 'title' => 'CE conformity', 'text' => 'Declarations of conformity can be downloaded as PDF from the product page.'],
                    ['icon' => 'target', 'title' => 'ISO 6789 calibration', 'text' => 'Torque wrenches ship with a calibration certificate; we remind you every 12 months.'],
                    ['icon' => 'settings', 'title' => 'Service in Türkiye', 'text' => 'Spare parts in stock, repair and calibration in our own workshop.'],
                    ['icon' => 'truck', 'title' => 'Delivery from stock', 'text' => 'Stock status and estimated lead time are shown on every product page.'],
                    ['icon' => 'whatsapp', 'title' => 'Ask an engineer', 'text' => 'Message us on WhatsApp and our engineer replies within an hour.'],
                ],
            ],
            'cta_band' => [
                'title' => "Get a quote\nfor your\nproject.",
                'text' => 'Not sure which product fits? Tell us the bolt size and torque, and we will recommend the right kit.',
                'button_text' => 'Open your quote',
            ],
            'torque_calculator' => [
                'eyebrow' => 'Engineering tools',
                'title' => "Torque\ncalculator",
                'text' => 'Choose bolt size and property class; see the required torque, the right wrench and pump pressure instantly.',
                'note' => 'The simple method uses T = K·F·d; the VDI 2230 method treats thread and under-head friction separately and includes the torsion from tightening. Yield values are from ISO 898-1, ASTM A193/A320 and SAE J429. For critical joints, use the bolt manufacturer\'s and project specification values.',
            ],
            'cylinder_selector' => [
                'eyebrow' => 'Engineering tools',
                'title' => "Cylinder selector\nand system builder",
                'text' => 'Enter the load, number of lifting points and required stroke; we recommend cylinders, pump, hoses and gauge as one kit.',
                'note' => 'The recommendation assumes the cylinders share the load equally. For eccentric loads, side forces or synchronous lifting, talk to our engineers.',
            ],
            'bolt_tensioner' => [
                'eyebrow' => 'Engineering tools',
                'title' => "Bolt tensioning\ncalculator",
                'text' => 'Enter the bolt, target preload and grip length; we calculate the tensioning load and pump pressure including load loss.',
                'note' => 'Load transfer factor LTF = 1.01 + diameter / grip length (minimum 1.10). The result is an approximation; where the residual load must be known exactly, measure bolt elongation before and after tensioning.',
            ],
            'unit_converter' => [
                'eyebrow' => 'Engineering tools',
                'title' => "Unit\nconverter",
                'text' => 'Type a value in any box; the other units in the group update instantly.',
                'note' => 'Tonne values are metric (1 t = 9.80665 kN). The US short ton is listed separately.',
            ],
            'flange_sequence' => [
                'eyebrow' => 'Engineering tools',
                'title' => "Flange bolt\ntightening sequence",
                'text' => 'Enter the number of bolts and the target torque; see the cross pattern, the torque for each pass and a step-by-step drawing.',
                'note' => 'Numbers are bolt positions clockwise (1 = 12 o\'clock). Values follow common practice; for gasketed joints, follow the gasket manufacturer\'s procedure and ASME PCC-1.',
            ],
            'spec_writer' => [
                'eyebrow' => 'Engineering tools',
                'title' => "Technical\nspecification writer",
                'text' => 'Choose the equipment and requirements for your purchase or tender file; clauses are written automatically, ready to copy or print.',
                'note' => 'Clauses follow common field practice and current regulatory references. Compare them with your organisation\'s requirements before adding them to a tender file. The text contains no brand or model references that would restrict competition.',
            ],
        ];
    }
}
