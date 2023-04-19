<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class detailProdukController extends Controller
{
    public function index($id){
        
        $products = $this->getProducts($id);
        $newProducts = $this->getNewProducts();

        dd($products, $newProducts);
        return view('customer.customer-detail-produk', ['products' => $products, 'newProducts' => $newProducts]);
    }

    public function getProducts($id){
        $products = DB::table('produk')
            ->select('*')
            ->where('id', '=', $id)
            ->where('status', '=', 'publish')
            ->get();
        
        $sales = DB::table('produk as prod')
        ->join('detail_produk as dp', 'dp.id_produk', '=' , 'prod.id')
        ->select('prod.id as id_produk', DB::raw('sum(dp.qty) as sales'))
        ->groupBy('prod.id')
        ->get();

        foreach ($products as $key => $product) {
            $products[$key]->sales =  0;
            foreach ($sales as $key2 => $sale) {
                if($products[$key]->id == $sales[$key2]->id_produk){
                    $products[$key]->sales =  $sales[$key2]->sales;
                }
            }            
        }

        return $products;
    }

    public function getNewProducts(){
        $newProducts = DB::table('produk as prod')
        ->select('*')
        ->where('status', '=', 'publish')
        ->limit(6)
        ->orderBy('prod.id', 'desc')
        ->get();

        $sales = DB::table('produk as prod')
        ->join('detail_produk as dp', 'dp.id_produk', '=' , 'prod.id')
        ->select('prod.id as id_produk', DB::raw('sum(dp.qty) as sales'))
        ->groupBy('prod.id')
        ->get();

        foreach ($newProducts as $key => $product) {
            $newProducts[$key]->sales =  0;
            foreach ($sales as $key2 => $sale) {
                if($newProducts[$key]->id == $sales[$key2]->id_produk){
                    $newProducts[$key]->sales =  $sales[$key2]->sales;
                }
            }            
        }
        
        return $newProducts;
    }
}