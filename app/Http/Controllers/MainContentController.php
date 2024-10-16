<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MainContentController extends Controller
{
    protected $editableIds = [
        'users' => 'id',
        'roles' => 'id',
        'product' => 'id_product',
        'sales' => 'id_sale',
        'sale_details' => 'id',
        'sellers' => 'id_saler',
        'suppliers' => 'id_supplier',
        'supplies' => 'id_supply',
        'supply_detail' => 'id_supply',
        'sellers_registered' => 'id',
        'storage' => 'id_product',
        'supplies_status' =>'id_supply'
    ];
    protected $editableColumns = [
        'users' => ['id','username','email', 'roleId'],
        'roles' => ['id','name'],
        'product' => ['id_product','name_product','price_product'],
        'sales' => ['id_sale', 'id_saler'],
        'sale_details' => ['id','id_sale', 'id_product', 'quantity','total_price'],
        'sellers' => ['id_saler','name_saler','telephone_saler','total_sells'],
        'suppliers' => ['id_supplier','name_org','name_supplier','email_supplier','telephone_supplier','adress_org'],
        'supplies' => ['id_supply','id_supplier','supply_date','quantity_products','total_price'],
        'supply_detail' => ['id_supply','id_product','quantity'],
        'sellers_registered' => ['id','id_saler'],
        'storage' => ['id_product','quantity_products'],
        'supplies_status' =>['id_supply','is_added']
    ];
    protected $tableNames = [
        'sellers',
        'product',
        'users',
        'suppliers',
        'supplies',
        'storage',
        'sales',
        'roles'
    ];
    protected $tableNamesTranslated = [
        'sellers' => 'Продавцы',
        'product' => 'Товары',
        'users' => 'Пользователи',
        'suppliers' => 'Поставщики',
        'supplies' => 'Поставки',
        'storage' => 'Склад',
        'sales' => 'Продажи',
        'roles' => 'Роли'
    ];

    protected $columnNames = [
        'users' => [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'email' => 'Электронная почта',
            'roleId' => 'Роль'
        ],
        'roles' => [
            'id' => 'ID',
            'name' => 'Название'
        ],
        'product' => [
            'id_product' => 'ID',
            'name_product' => 'Название товара',
            'price_product' => 'Цена товара'
        ],
        'sales' => [
            'id_sale' => 'ID',
            'id_saler' => 'ID продавца'
        ],
        'sale_details' => [
            'id' => 'ID',
            'id_sale' => 'ID продажи',
            'id_product' => 'ID товара',
            'quantity' => 'Количество',
            'total_price' => 'Общая цена'
        ],
        'sellers' => [
            'id_saler' => 'ID',
            'name_saler' => 'Имя продавца',
            'telephone_saler' => 'Телефон продавца',
            'total_sells' => 'Всего продаж'
        ],
        'suppliers' => [
            'id_supplier' => 'ID',
            'name_org' => 'Название организации',
            'name_supplier' => 'Имя поставщика',
            'email_supplier' => 'Электронная почта',
            'telephone_supplier' => 'Телефон поставщика',
            'adress_org' => 'Адрес организации'
        ],
        'supplies' => [
            'id_supply' => 'ID',
            'id_supplier' => 'ID поставщика',
            'supply_date' => 'Дата поставки',
            'quantity_products' => 'Количество товаров',
            'total_price' => 'Общая цена (скидка 13%)'
        ],
        'supply_detail' => [
            'id_supply' => 'ID поставки',
            'id_product' => 'ID товара',
            'quantity' => 'Количество'
        ],
        'sellers_registered' => [
            'id' => 'ID',
            'id_saler' => 'ID продавца'
        ],
        'storage' => [
            'id_product' => 'ID товара',
            'quantity_products' => 'Количество товаров'
        ],
        'supplies_status' => [
            'id_supply' => 'ID поставки',
            'is_added' => 'Добавлено'
        ]
    ];
    public function showTables(){
    $user = Auth::user();
    $roleId = $user->roleId;
    if($roleId == 0 || $roleId == 1){
        return view('tables', ['tables' => $this->tableNames, 'tableNamesTranslated' => $this->tableNamesTranslated]);
    }
        return redirect(route('user.home'));
    }
    public function showHome(Request $request) {
        $user = Auth::user();   
        return view('home');
    }
           
    public function editTable($tableName) {
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            $tableId = $this->getTableIdColumn($tableName);
            $tableData = DB::table($tableName)->orderBy($tableId)->get();
    
            return view('editTable', [
                'tableName' => $tableName,
                'columns' => $columns,
                'tableData' => $tableData,
                'tableId' => $tableId,
                'editableColumns' => $this->editableColumns[$tableName],
                'columnNames' => $this->columnNames,
                'tableNamesTranslated' => $this->tableNamesTranslated
            ]);
        } else {
            abort(404);
        }
    }
    
    private function getTableIdColumn($tableName) {
        return $this->editableIds[$tableName];
    }
    
    
    public function destroy($tableName, $id)
    {

        $tableId = $this->editableColumns[$tableName][0];
        DB::table($tableName)->where($tableId, $id)->delete();
        return redirect()->route('user.tables.edit', $tableName)->with('success', 'Строка успешно удалена');
    }
    
    //TODO
    public function updateTable(Request $request, $tableName, $id) {

        if (!array_key_exists($tableName, $this->editableColumns)) {
            abort(404);
        }
    
        $updateData = [];
        foreach ($this->editableColumns[$tableName] as $column) {
            $updateData[$column] = $request->input($column);
        }
        if($tableName != 'roles')
        {
            $updateData['updated_at'] = now();
        }
        $tableId = $this->editableColumns[$tableName][0];
        DB::table($tableName)
            ->where($tableId, $id)
            ->update($updateData);
    
        return redirect()->route('user.tables.edit', $tableName)->with('success', 'Данные успешно обновлены');
    }
    
    public function addTable(Request $request, $tableName) {

    
        if (!array_key_exists($tableName, $this->editableColumns)) {
            abort(404);
        }
    
        $newData = [];
        foreach ($this->editableColumns[$tableName] as $column) {
            $newData[$column] = $request->input($column);
        }
        if ($tableName != 'roles'){
        $newData['created_at'] = now();
        $newData['updated_at'] = now();
    }
        DB::table($tableName)->insert($newData);
        return redirect()->route('user.tables.edit', $tableName)->with('success', 'Запись успешно добавлена');
    }
     

    public function showSales()
    {
        if(Auth::user()->roleId == 2) {
            $products = DB::table('storage')
                          ->join('product', 'storage.id_product', '=', 'product.id_product')
                          ->select('product.id_product', 'product.name_product', 'storage.quantity_products')
                             ->get();

            return view('sales', compact('products'));
        }
        return redirect(route('user.home'));
    }

    public function storeSales(Request $request)
{
    $request->validate([
        'products' => 'required|array',
        'products.*.product_id' => 'required|exists:product,id_product',
        'products.*.quantity' => 'required|integer|min:1',
        'sale_date' => 'required|date'
    ]);

    $sellerRecord = DB::table('sellers_registered')->select('id_saler')->where('id', Auth::user()->id)->first();
    $sellerId = $sellerRecord ? $sellerRecord->id_saler : null;

    if (!$sellerId) {
        return back()->withErrors(['seller' => 'Продавец не найден.']);
    }

    $errors = [];
    $totalPrices = [];

    // Создаем уникальный идентификатор продажи
    $saleId = Str::uuid();

    foreach ($request->products as $index => $productRequest) {
        $product = DB::table('storage')
                     ->join('product', 'storage.id_product', '=', 'product.id_product')
                     ->where('storage.id_product', $productRequest['product_id'])
                     ->select('storage.quantity_products', 'product.name_product', 'product.price_product')
                     ->first();

        if ($product->quantity_products < $productRequest['quantity']) {
            $errors["products.$index.quantity"] = 'Недостаточное количество товаров на складе для ' . $product->name_product;
            continue;
        }

        $totalPrice = $product->price_product * $productRequest['quantity'];
        $totalPrices[] = [
            'id_sale' => $saleId,
            'id_product' => $productRequest['product_id'],
            'quantity' => $productRequest['quantity'],
            'total_price' => $totalPrice,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('storage')->where('id_product', $productRequest['product_id'])->decrement('quantity_products', $productRequest['quantity']);
    }

    if (!empty($errors)) {
        return back()->withErrors($errors)->withInput();
    }

    DB::table('sales')->insert([
        'id_sale' => $saleId,
        'id_saler' => $sellerId,
        'sale_date' => $request->sale_date,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    DB::table('sale_details')->insert($totalPrices);

    DB::table('sellers')->where('id_saler', $sellerId)->increment('total_sells');

    return redirect()->route('user.sales')->with('success', 'Товары успешно проданы');
}

public function showSupplies() {
    $user = Auth::user();
    $roleId = $user->roleId;
    if ($roleId == 0 || $roleId == 1) {
        $suppliers = DB::table('suppliers')->get();
        $products = DB::table('product')->get();
        return view('supplies', ['suppliers' => $suppliers, 'products' => $products]);
    }
    return redirect(route('user.home'));
}

public function storeSupply(Request $request) {
    $validated = $request->validate([
        'supplier_id' => 'required|exists:suppliers,id_supplier',
        'supply_date' => 'required|date',
        'products' => 'required|array',
        'products.*.product_id' => 'required|exists:product,id_product',
        'products.*.quantity' => 'required|integer|min:1'
    ]);

    $supplierId = $validated['supplier_id'];
    $supplyDate = $validated['supply_date'];
    $products = $validated['products'];

    $totalPrice = 0;

    foreach ($products as $product) {
        $productData = DB::table('product')->where('id_product', $product['product_id'])->first();
        $totalPrice += $productData->price_product * $product['quantity'];
    }

    $totalPrice *= 0.87;
    $totalPrice = round($totalPrice);

    $supplyId = DB::table('supplies')->insertGetId([
        'id_supplier' => $supplierId,
        'supply_date' => $supplyDate,
        'quantity_products' => array_sum(array_column($products, 'quantity')),
        'total_price' => $totalPrice,
        'created_at' => now(),
        'updated_at' => now()
    ], 'id_supply');

    foreach ($products as $product) {
        DB::table('supply_detail')->insert([
            'id_supply' => $supplyId,
            'id_product' => $product['product_id'],
            'quantity' => $product['quantity'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    DB::table('supplies_status')->insert([
        'id_supply' => $supplyId,
        'is_added' => false,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    Artisan::call('transfer:supplies');

    return redirect()->route('user.supplies')->with('success', 'Поставка успешно добавлена и товары перемещены на склад.');
}

public function getSaleDetails($id_sale) {
    $saleDetails = DB::table('sale_details')
        ->join('product', 'sale_details.id_product', '=', 'product.id_product')
        ->join('sales', 'sale_details.id_sale', '=', 'sales.id_sale')
        ->join('sellers', 'sales.id_saler', '=', 'sellers.id_saler')
        ->where('sale_details.id_sale', $id_sale)
        ->select('sale_details.id_sale', 'product.name_product', 'sale_details.quantity', 'sales.sale_date', 'sellers.name_saler', 'product.price_product', DB::raw('sale_details.quantity * product.price_product as total_price'))
        ->get();
    return response()->json($saleDetails);
}

public function getSupplyDetails($id_supply) {
    $supplyDetails = DB::table('supply_detail')
        ->join('product', 'supply_detail.id_product', '=', 'product.id_product')
        ->join('supplies', 'supply_detail.id_supply', '=', 'supplies.id_supply')
        ->join('suppliers', 'supplies.id_supplier', '=', 'suppliers.id_supplier')
        ->where('supply_detail.id_supply', $id_supply)
        ->select('supply_detail.id_supply', 'suppliers.name_supplier', 'supplies.supply_date', 'product.name_product', 'supply_detail.quantity', 'product.price_product', DB::raw('supply_detail.quantity * product.price_product as total_price'))
        ->get();
    return response()->json($supplyDetails);
}

public function getProductDetails($id_product) {
    $productDetails = DB::table('product')
        ->where('id_product', $id_product)
        ->select('id_product', 'name_product', 'price_product')
        ->first();
    return response()->json($productDetails);
}

}