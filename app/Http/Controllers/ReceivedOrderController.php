<?php

namespace App\Http\Controllers;

use App\Models\ReceivedOrder;
use App\Http\Requests\StoreReceivedOrderRequest;
use App\Http\Requests\UpdateReceivedOrderRequest;

class ReceivedOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReceivedOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceivedOrder $receivedOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReceivedOrderRequest $request, ReceivedOrder $receivedOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceivedOrder $receivedOrder)
    {
        //
    }
}
