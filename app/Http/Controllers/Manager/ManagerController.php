<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\ShopStaff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data['products'] = Product::all();
        return view('dashboard.manager.menuProducts.index')->with($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add_product_to_menu($id)
    {
        $shopStaff = ShopStaff::where('user_id', auth()->user()->id)->first();
        $shopProduct = ShopProduct::where('shop_id', $shopStaff->shop_id)->where('product_id', $id)->first();
        if (!$shopProduct){
            $data['shop_id'] = $shopStaff->shop_id;
            $data['product_id'] = $id;
            ShopProduct::create($data);
            return redirect()->route('menu_management')->with('message', 'Product added to menu successfully');
        }
        else
        {
            return redirect()->back()->with('error', 'This product is already added to the menu');
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function shop_products()
    {
        $shopStaff = ShopStaff::where('user_id', Auth::user()->id)->first();
        $data['shop'] = Shop::where('id', $shopStaff->shop_id)->with('products')->first();
        return view('dashboard.manager.menuProducts.menuManagement')->with($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove_from_menu($id)
    {
        $shopStaff = ShopStaff::where('user_id', Auth::user()->id)->first();
        $product = ShopProduct::where('shop_id', $shopStaff->shop_id)->where('product_id', $id)->first();
        $product->delete();
        return redirect()->back()->with('message', 'Product removed from menu successfully');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function order_history()
    {
        $shopStaff = ShopStaff::where('user_id', Auth::user()->id)->first();
        $data['orders'] = Order::where('shop_id', $shopStaff->shop_id)->with('product')->get();
        return view('dashboard.manager.order.orderHistory')->with($data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function get_shop_time()
    {
        $shopStaff = ShopStaff::where('user_id', Auth::user()->id)->first();
        $data['shop'] = Shop::where('id', $shopStaff->shop_id)->first();
        return view('dashboard.manager.shop.form')->with($data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function shop_time_index()
    {
        $shopStaff = ShopStaff::where('user_id', Auth::user()->id)->first();
        $data['shop'] = Shop::where('id', $shopStaff->shop_id)->first();
        return view('dashboard.manager.shop.index')->with($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_shop_time(Request $request)
    {
        $shopStaff = ShopStaff::where('user_id', Auth::user()->id)->first();
        $shop = Shop::where('id', $shopStaff->shop_id)->first();
        $shop->update($request->only(['opening_time', 'closing_time']));
        return redirect()->route('shop_time_index')->with('message', 'Shop time updated successfully');
    }
}
