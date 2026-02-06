<?php

namespace App\Data;

use App\Models\Property;

class PropertyDto
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly int $bedrooms,
        public readonly int $bathrooms,
        public readonly int $storeys,
        public readonly int $garages,
    )
    {
    }

    public static function fromModel(Property $property): self
    {
        return new self(
            name: $property->name,
            price: $property->price,
            bedrooms: $property->bedrooms,
            bathrooms: $property->bathrooms,
            storeys: $property->storeys,
            garages: $property->garages,
        );
    }
}
