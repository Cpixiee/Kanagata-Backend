@foreach($reviewRequests as $request)
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200" data-category="{{ strtolower($request->model_type) }}">
    <div class="flex justify-between items-start mb-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 rounded-lg {{ $request->action_type === 'create' ? 'bg-green-100' : ($request->action_type === 'update' ? 'bg-blue-100' : 'bg-red-100') }}">
                    @if($request->action_type === 'create')
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    @elseif($request->action_type === 'update')
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ ucfirst($request->action_type) }} {{ $request->model_type }}</h3>
                    <p class="text-sm text-gray-500">{{ $request->model_type }} Management Request</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="space-y-2">
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-gray-600">Requested by:</span>
                        <span class="font-medium ml-1">{{ $request->user->name }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium ml-1">{{ $request->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
                
                @if(isset($request->data['coa']) || isset($request->data['customer']) || isset($request->data['activity']))
                <div class="space-y-2">
                    @if(isset($request->data['coa']))
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-gray-600">COA:</span>
                        <span class="font-medium ml-1">{{ $request->data['coa'] }}</span>
                    </div>
                    @endif
                    @if(isset($request->data['customer']))
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-medium ml-1">{{ $request->data['customer'] }}</span>
                    </div>
                    @endif
                    @if(isset($request->data['activity']))
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-gray-600">Activity:</span>
                        <span class="font-medium ml-1">{{ $request->data['activity'] }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            @if(isset($request->data['debit']) || isset($request->data['credit']) || isset($request->data['rate_1']) || isset($request->data['rate_2']))
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <h5 class="text-sm font-medium text-gray-700 mb-2">Financial Information</h5>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    @if(isset($request->data['debit']))
                    <div>
                        <span class="text-gray-500">Debit:</span>
                        <span class="font-medium text-green-600">Rp {{ number_format($request->data['debit'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if(isset($request->data['credit']))
                    <div>
                        <span class="text-gray-500">Credit:</span>
                        <span class="font-medium text-red-600">Rp {{ number_format($request->data['credit'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if(isset($request->data['rate_1']))
                    <div>
                        <span class="text-gray-500">Rate 1:</span>
                        <span class="font-medium">Rp {{ number_format($request->data['rate_1'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if(isset($request->data['rate_2']))
                    <div>
                        <span class="text-gray-500">Rate 2:</span>
                        <span class="font-medium">Rp {{ number_format($request->data['rate_2'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        <div class="flex items-center space-x-3">
            @if($request->status === 'pending')
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Pending
                </span>
            @elseif($request->status === 'approved')
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Approved
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-red-100 text-red-800 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Rejected
                </span>
            @endif
        </div>
    </div>
    
    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <div class="flex items-center space-x-3">
            <button type="button" 
                data-request-id="{{ $request->id }}"
                class="view-details inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Details
            </button>
            @if($request->status === 'pending')
            <button type="button" 
                data-request-id="{{ $request->id }}"
                class="approve-request inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Approve
            </button>
            <button type="button" 
                data-request-id="{{ $request->id }}"
                class="reject-request inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reject
            </button>
            @endif
        </div>
        
        <div class="text-xs text-gray-500">
            ID: #{{ $request->id }}
        </div>
    </div>
</div>
@endforeach

@if($reviewRequests->isEmpty())
<div class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">No review requests found</h3>
    <p class="mt-1 text-sm text-gray-500">No requests match your current filters.</p>
</div>
@endif 