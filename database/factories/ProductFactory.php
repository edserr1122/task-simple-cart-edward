<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productTypes = [
            'Wireless Headphones', 'Smart Watch', 'Laptop Stand', 'USB-C Cable',
            'Bluetooth Speaker', 'Phone Case', 'Tablet Stand', 'Keyboard',
            'Computer Mouse', 'Webcam', 'Microphone', 'Monitor',
            'Desk Lamp', 'USB Hub', 'External Hard Drive', 'SSD Drive',
            'Memory Card', 'Power Bank', 'Charging Station', 'Cable Organizer',
            'Desk Mat', 'Laptop Bag', 'Backpack', 'Water Bottle',
            'Coffee Mug', 'Desk Organizer', 'Monitor Stand', 'Ergonomic Chair',
            'Standing Desk', 'Desk Fan', 'Air Purifier', 'Desk Plant',
            'Notebook', 'Pen Set', 'Stapler', 'Paper Clips',
            'Sticky Notes', 'File Folder', 'Binder', 'Calculator',
            'Ruler', 'Scissors', 'Tape Dispenser', 'Whiteboard',
            'Marker Set', 'Eraser', 'Highlighter', 'Index Cards',
            'Bookend', 'Desk Clock', 'Calendar', 'Planner',
            'Desk Light', 'Cable Management', 'Laptop Sleeve', 'Tablet Case',
            'Phone Stand', 'Car Mount', 'Wall Mount', 'Tripod',
        ];

        $adjectives = ['Premium', 'Pro', 'Ultra', 'Deluxe', 'Standard', 'Basic', 'Elite', 'Advanced'];

        return [
            'name' => fake()->randomElement($adjectives) . ' ' . fake()->unique()->randomElement($productTypes),
            'price' => fake()->randomFloat(2, 5.99, 999.99),
            'stock_quantity' => fake()->numberBetween(0, 100),
        ];
    }
}
