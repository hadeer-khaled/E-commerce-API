<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

use App\Models\Category;


class CategoriesImport implements ToCollection , WithHeadingRow  , WithValidation
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            $category = Category::where('title' , $row['title'])->first();
            if($category){
                $category->update([
                    'title' => $row['title'],
                ]);
            }
            else
            {
                Category::create([
                    'title' => $row['title'],
                ]);
            }
        }
    }
    public function rules(): array
    {
        return [
            '*.title' => 'required|string|max:50',
        ];
    }
}
