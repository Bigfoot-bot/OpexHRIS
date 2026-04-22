@extends('tenant.layouts.app')
@section('page-title', 'New Expense Claim')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('tenant.expenses.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Employee *</label>
                    <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Claim Date *</label>
                    <input type="date" name="claim_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Field Visit to Kisumu" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Expense Items</h3>
                    <button type="button" onclick="addItem()" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">+ Add Item</button>
                </div>
                <div id="items-container" class="space-y-3">
                    <div class="item-row grid grid-cols-5 gap-2 p-3 bg-gray-50 rounded-lg">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Category</label>
                            <select name="items[0][category]" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="transport">Transport</option>
                                <option value="accommodation">Accommodation</option>
                                <option value="meals">Meals</option>
                                <option value="supplies">Supplies</option>
                                <option value="communication">Communication</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Description *</label>
                            <input type="text" name="items[0][description]" required placeholder="Description" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Date</label>
                            <input type="date" name="items[0][date]" value="{{ date('Y-m-d') }}" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Amount (KES) *</label>
                            <input type="number" name="items[0][amount]" required placeholder="0.00" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Receipt</label>
                            <input type="file" name="items[0][receipt]" class="w-full text-xs"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium px-6 py-2 rounded-lg">Save Claim</button>
                <a href="{{ route('tenant.expenses.index') }}" class="bg-gray-100 text-gray-600 text-sm font-medium px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
let itemCount = 1;
function addItem() {
    const container = document.getElementById('items-container');
    const div = document.createElement('div');
    div.className = 'item-row grid grid-cols-5 gap-2 p-3 bg-gray-50 rounded-lg';
    div.innerHTML = `
        <div><label class="block text-xs text-gray-500 mb-1">Category</label>
        <select name="items[${itemCount}][category]" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
            <option value="transport">Transport</option><option value="accommodation">Accommodation</option>
            <option value="meals">Meals</option><option value="supplies">Supplies</option>
            <option value="communication">Communication</option><option value="other">Other</option>
        </select></div>
        <div><label class="block text-xs text-gray-500 mb-1">Description *</label>
        <input type="text" name="items[${itemCount}][description]" placeholder="Description" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/></div>
        <div><label class="block text-xs text-gray-500 mb-1">Date</label>
        <input type="date" name="items[${itemCount}][date]" value="{{ date('Y-m-d') }}" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/></div>
        <div><label class="block text-xs text-gray-500 mb-1">Amount (KES) *</label>
        <input type="number" name="items[${itemCount}][amount]" placeholder="0.00" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"/></div>
        <div><label class="block text-xs text-gray-500 mb-1">Receipt</label>
        <input type="file" name="items[${itemCount}][receipt]" class="w-full text-xs"/>
        <button type="button" onclick="this.closest('.item-row').remove()" class="text-xs text-red-500 mt-1">Remove</button></div>`;
    container.appendChild(div);
    itemCount++;
}
</script>
@endsection
