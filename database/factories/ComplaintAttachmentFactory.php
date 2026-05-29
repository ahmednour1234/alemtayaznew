<?php

namespace Database\Factories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintAttachmentFactory extends Factory
{
    public function definition(): array
    {
        $ext = fake()->randomElement(['pdf', 'jpg', 'png', 'docx']);

        return [
            'complaint_id'  => Complaint::factory(),
            'path'          => 'complaints/attachments/' . fake()->uuid() . '.' . $ext,
            'original_name' => fake()->word() . '.' . $ext,
            'mime_type'     => match ($ext) {
                'pdf'  => 'application/pdf',
                'jpg'  => 'image/jpeg',
                'png'  => 'image/png',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            },
            'size' => fake()->numberBetween(1024, 5242880),
        ];
    }
}
