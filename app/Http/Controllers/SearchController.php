<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Neon\Services\LinkService;

class SearchController extends Controller
{
    use ValidatesRequests;

    public function search(LinkService $page_service, Request $request)
    {
        $page       = $page_service->static('search');

        $this->validate($request, [
            'query' => 'required|max:255'
        ]);

        $search_term = $request->input('query');

        $search_result = Product::with('categories')
            ->with('brand')
            ->where(function($query) use ($search_term) {
                $query->where('product_id', 'LIKE', "%{$search_term}%")
                    ->orWhere('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('description', 'LIKE', "%{$search_term}%")
                    ->orWhere('packaging', 'LIKE', "%{$search_term}%")
                    ->orWhere('ean', 'LIKE', "%{$search_term}%");
            })
            ->orWhereHas('categories', function($query) use ($search_term) {
                $query->where('name', 'LIKE', "%{$search_term}%");
            })
            ->orWhereHas('brand', function($query) use ($search_term) {
                $query->where('name', 'LIKE', "%{$search_term}%");
            })
            ->get();
         
        return View::first(
            $page_service->getViews(Arr::first(app('site')->current()->domains)),
            [
                'page'              => $page,
                'search_result'     => $search_result,
                'search_term'       => $search_term
            ]
        );
    }
}
