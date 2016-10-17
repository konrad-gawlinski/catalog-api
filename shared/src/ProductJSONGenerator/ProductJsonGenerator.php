<?php

class ProductJsonGenerator
{
    private $nameGenerator;

    public function __construct(NameGenerator $nameGenerator)
    {
        $this->nameGenerator = $nameGenerator;
    }

    public function generateJSON()
    {
        return <<<JSON
{
    "variety": "config",
    "type": "{$this->generateType()}",
    "name": "{$this->nameGenerator->generateUniqueName()}",
    "manufacturer": "{$this->generateManufacturer()}",
    "price": {$this->generatePrice()},
    "tax_rate": 19,
    "seo_robots": [{$this->generateSeoRobots()}],
    "ingredient_list": 1,
    "label_language": [{$this->generateLabelLanguages()}],
    "attributes" : [{$this->generateAttributes()}]                
}
JSON;

    }

    private function generateSeoRobots()
    {
        $seo_robots = [];
        if (rand(0,9) %2) {
            $in = ['"index"', '"noindex"'];
            $seo_robots[] = $in[array_rand($in, 1)];
        }

        if (rand(0,9) %2) {
            $in = ['"follow"', '"nofollow"'];
            $seo_robots[] = $in[array_rand($in, 1)];
        }

        return implode(',', $seo_robots);
    }

    private function generateLabelLanguages()
    {
        $in = ['"de"', '"pl"', '"sp"', '"en"', '"it"', '"dk"', '"cn"'];
        $label_language = (array) array_rand($in, rand(1, count($in)-1));
        $label_language = array_map(function($key) use ($in) {
            return $in[$key];
        }, $label_language);

        return implode(',', $label_language);
    }

    private function generatePrice()
    {
        return rand(100, 10000);
    }

    private function generateManufacturer()
    {
        $in = ['nu3', 'philips', 'apple', 'microsoft', 'home24', 'nike', 'adidas'];

        return $in[array_rand($in)];
    }

    private function generateType()
    {
        $in = ['consumer', 'book', 'voucher'];

        return $in[array_rand($in, 1)];
    }

    private function generateAttributes()
    {
        $in = [
        '"is_vegetarian"',
        '"is_gluten_free"',
        '"is_gelatine_free"',
        '"is_sugar_free"',
        '"no_sugar_substitutes"',
        '"is_lactose_free"',
        '"no_artificial_flavours"',
        '"only_natural_ingredients"'
        ];
        
        $attributes = (array) array_rand($in, rand(1, 4));
        $attributes = array_map(function($key) use ($in) {
            return $in[$key];
        }, $attributes);

        return implode(',', $attributes);
    }
}