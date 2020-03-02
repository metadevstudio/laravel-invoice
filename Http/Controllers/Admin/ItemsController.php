<?php

namespace Modules\Invoices\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Invoices\Entities\Invoice;
use Modules\Invoices\Entities\InvoiceItem;
use Modules\Invoices\Entities\Item;

/**
 * Class ItemsController
 * @package Modules\Invoices\Http\Controllers\Admin
 */
class ItemsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $items = Item::all();

        return view('invoices::admin.items', [
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('invoices::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'value' => 'required',
            'type' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $item = new Item();
        $item->fill($request->except(['token']));

        try {
            $item->save();
            return redirect()->back()->with([
                'message' => 'Item cadastrado com sucesso.',
                'icon' => 'fas fa-thumbs-up',
                'color' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Ops, ocorreu um erro: ' . $e->getMessage(),
                'icon' => 'fas fa-thumbs-down',
                'color' => 'danger',
            ]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('invoices::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('invoices::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $item = Item::findOrFail($request->item_id);
        try {
            $item->update($request->except(['_token', 'item_id']));
            return redirect()->back()->with([
                'message' => "Item atualizado com sucesso",
                'icon' => 'fas fa-thumbs-up',
                'color' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Ops, ocorreu um erro: ' . $e->getMessage(),
                'icon' => 'fas fa-thumbs-down',
                'color' => 'danger',
            ]);
        }
    }

    /**
     * Atualiza um item da fatura
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateInvoiceItem(Request $request)
    {
        $item = InvoiceItem::findOrFail($request->item_id);
        try {
            $item->name = $request->item_name;
            $item->value = $request->item_value;
            $item->type = $request->item_type;
            $item->amount = $request->amount;
            $item->description = $request->description;
            $item->save();

            return redirect()->back()->with([
                'message' => "Item atualizado com sucesso",
                'icon' => 'fas fa-thumbs-up',
                'color' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Ops, ocorreu um erro: ' . $e->getMessage(),
                'icon' => 'fas fa-thumbs-down',
                'color' => 'danger',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        try {
            $item->delete();
            return redirect()->back()->with([
                'message' => "Item excluído com sucesso",
                'icon' => 'fas fa-thumbs-up',
                'color' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Ops, ocorreu um erro: ' . $e->getMessage(),
                'icon' => 'fas fa-thumbs-down',
                'color' => 'danger',
            ]);
        }
    }

    /**
     * Obtém os dados de um item do tipo \Modules\Invoices\Entities\Item.php
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        $json = [];
        /** @var Item $item */
        $item = Item::find($request->id);

        if (empty($item)) {
            $json['success'] = false;
            return response()->json($json);
        }

        $json['success'] = true;
        $json['data'] = $item->toArray();
        return response()->json($json);
    }

    /**
     * Obtém os dados de um item da fatura do tipo \Modules\Invoices\Entities\InvoiceItem.php
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceItemData(Request $request)
    {
        $json = [];
        /** @var Item $item */
        $item = InvoiceItem::find($request->id);

        if (empty($item)) {
            $json['success'] = false;
            return response()->json($json);
        }

        $json['success'] = true;
        $json['data'] = $item->toArray();
        $json['data']['value'] = str_replace('.', ',', $item->value);
        return response()->json($json);
    }
}
