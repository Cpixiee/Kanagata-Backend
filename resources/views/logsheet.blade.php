<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kanagata - Logsheet</title>
    <link rel="stylesheet" href="{{ asset('src/output.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <link rel="stylesheet" href="{{ asset('src/scroll-hover.css') }}">
    <link rel="stylesheet" href="{{ asset('src/table-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/logsheet.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</head>

<body class="font-poppins" 
    data-success="{{ Session::get('success') }}"
    data-error="{{ Session::get('error') }}"
    data-role="{{ Auth::user()->role }}">
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>
                    <a href="" class="flex ms-2 md:me-24">
                        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="FlowBite Logo" />
                        <span
                            class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Kanagata</span>
                    </a>
                </div>
                <div>
                    <h1 class="self-center text-xl sm:text-2xl font-semibold dark:text-white">Logsheet</h1>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div>
                            <button type="button"
                                class="flex text-sm bg-gray-800 dark:bg-white rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <!-- <img class="w-8 h-8 rounded-full"
                                    src="https://flowbite.com/docs/images/people/profile-picture-5.jpg"
                                    alt="user photo"> -->
                                <svg class="w-8 h-8 p-1 text-white dark:text-gray-800" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600"
                            id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ Auth::user()->email }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    Role: {{ Auth::user()->role }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white text-left">
                                            Sign out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
        aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M13.5 2c-.178 0-.356.013-.492.022l-.074.005a1 1 0 0 0-.934.998V11a1 1 0 0 0 1 1h7.975a1 1 0 0 0 .998-.934l.005-.074A7.04 7.04 0 0 0 22 10.5 8.5 8.5 0 0 0 13.5 2Z" />
                            <path
                                d="M11 6.025a1 1 0 0 0-1.065-.998 8.5 8.5 0 1 0 9.038 9.039A1 1 0 0 0 17.975 13H11V6.025Z" />
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('insight') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Insight</span>
                    </a>
                </li>
                @if(Auth::check() && Auth::user()->role === 'admin')
                <li>
                    <a href="{{ route('review.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036a2.63 2.63 0 0 0 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258a2.63 2.63 0 0 0-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.63 2.63 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.63 2.63 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Review</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('projects.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 25 25">
                            <path fill-rule="evenodd"
                                d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Project</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M14 7h-4v3a1 1 0 0 1-2 0V7H6a1 1 0 0 0-.997.923l-.917 11.924A2 2 0 0 0 6.08 22h11.84a2 2 0 0 0 1.994-2.153l-.917-11.924A1 1 0 0 0 18 7h-2v3a1 1 0 1 1-2 0V7Zm-2-3a2 2 0 0 0-2 2v1H8V6a4 4 0 0 1 8 0v1h-2V6a2 2 0 0 0-2-2Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Customer</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tutor.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M12 6a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm-1.5 8a4 4 0 0 0-4 4 2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-3Zm6.82-3.096a5.51 5.51 0 0 0-2.797-6.293 3.5 3.5 0 1 1 2.796 6.292ZM19.5 18h.5a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-1.1a5.503 5.503 0 0 1-.471.762A5.998 5.998 0 0 1 19.5 18ZM4 7.5a3.5 3.5 0 0 1 5.477-2.889 5.5 5.5 0 0 0-2.796 6.293A3.501 3.501 0 0 1 4 7.5ZM7.1 12H6a4 4 0 0 0-4 4 2 2 0 0 0 2 2h.5a5.998 5.998 0 0 1 3.071-5.238A5.505 5.505 0 0 1 7.1 12Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Tutor</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('logsheet.index') }}"
                        class="flex items-center p-2 {{ request()->routeIs('logsheet.*') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('logsheet.*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M5.024 3.783A1 1 0 0 1 6 3h12a1 1 0 0 1 .976.783L20.802 12h-4.244a1.99 1.99 0 0 0-1.824 1.205 2.978 2.978 0 0 1-5.468 0A1.991 1.991 0 0 0 7.442 12H3.198l1.826-8.217ZM3 14v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5h-4.43a4.978 4.978 0 0 1-9.14 0H3Zm5-7a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm0 2a1 1 0 0 0 0 2h8a1 1 0 1 0 0-2H8Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Logsheet</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('invoice.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1V10H5Zm4 0v10h10a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H9Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Invoice</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ledger.index') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M10.915 2.345a2 2 0 0 1 2.17 0l7 4.52A2 2 0 0 1 21 8.544V9.5a1.5 1.5 0 0 1-1.5 1.5H19v6h1a1 1 0 1 1 0 2H4a1 1 0 1 1 0-2h1v-6h-.5A1.5 1.5 0 0 1 3 9.5v-.955a2 2 0 0 1 .915-1.68l7-4.52ZM17 17v-6h-2v6h2Zm-6-6h2v6h-2v-6Zm-2 6v-6H7v6h2Z"
                                clip-rule="evenodd" />
                            <path d="M2 21a1 1 0 0 1 1-1h18a1 1 0 1 1 0 2H3a1 1 0 0 1-1-1Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Ledger</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="p-4 sm:ml-64">
        <div id="logsheet-details" class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold font-poppins">Logsheet</h1>
                <div class="flex">
                    <button type="button" data-modal-target="add-logsheet-modal" data-modal-toggle="add-logsheet-modal" class="flex items-center text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 hover:bg-blue-200 dark:hover:bg-blue-700 px-4 py-2 rounded-lg whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Entry
                    </button>
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table id="logsheet-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 text-center uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3" data-orderable="false">No</th>
                            <th scope="col" class="px-6 py-3">COA</th>
                            <th scope="col" class="px-6 py-3">Customer</th>
                            <th scope="col" class="px-6 py-3">Activity</th>
                            <th scope="col" class="px-6 py-3">Prodi</th>
                            <th scope="col" class="px-6 py-3">Grade</th>
                            <th scope="col" class="px-6 py-3">Seq</th>
                            <th scope="col" class="px-6 py-3">Quantity (School)</th>
                            <th scope="col" class="px-6 py-3">Rate (School)</th>
                            <th scope="col" class="px-6 py-3">Revenue</th>
                            <th scope="col" class="px-6 py-3">AR Status</th>
                            <th scope="col" class="px-6 py-3">Tutor</th>
                            <th scope="col" class="px-6 py-3">Quantity (Tutor)</th>
                            <th scope="col" class="px-6 py-3">Rate (Tutor)</th>
                            <th scope="col" class="px-6 py-3">Cost</th>
                            <th scope="col" class="px-6 py-3">AP Status</th>
                            <th scope="col" class="px-6 py-3" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($logsheets as $logsheet)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">{{ $logsheet->coa }}</td>
                            <td class="px-6 py-4">{{ $logsheet->customer }}</td>
                            <td class="px-6 py-4">{{ $logsheet->activity }}</td>
                            <td class="px-6 py-4">{{ $logsheet->prodi }}</td>
                            <td class="px-6 py-4">{{ $logsheet->grade }}</td>
                            <td class="px-6 py-4">{{ $logsheet->seq }}</td>
                            <td class="px-6 py-4">{{ $logsheet->quantity_1 }}</td>
                            <td class="px-6 py-4">{{ number_format($logsheet->rate_1, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ number_format($logsheet->revenue, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($logsheet->ar_status === 'Paid')
                                    <span class="text-green-600">Paid</span>
                                @elseif($logsheet->ar_status === 'Pending')
                                    <span class="text-gray-600">Listing</span>
                                @else
                                    <span class="text-gray-600">{{ $logsheet->ar_status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $logsheet->tutor }}</td>
                            <td class="px-6 py-4">{{ $logsheet->quantity_2 }}</td>
                            <td class="px-6 py-4">{{ number_format($logsheet->rate_2, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ number_format($logsheet->cost, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($logsheet->ap_status === 'Paid')
                                    <span class="text-green-600">Paid</span>
                                @elseif($logsheet->ap_status === 'Pending')
                                    <span class="text-gray-600">Listing</span>
                                @else
                                    <span class="text-gray-600">{{ $logsheet->ap_status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 flex justify-center">
                                <button type="button" 
                                    data-modal-target="edit-logsheet-modal" 
                                    data-modal-toggle="edit-logsheet-modal"
                                    data-logsheet-id="{{ $logsheet->id }}" 
                                    class="mx-2 font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 hover:bg-blue-200 dark:hover:bg-blue-700 px-4 py-1 rounded-md edit-logsheet">
                                    Edit
                                </button>
                                <form action="{{ route('logsheet.destroy', $logsheet->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-logsheet mx-2 font-medium text-red-600 dark:text-red-200 bg-red-100 dark:bg-red-600 hover:bg-red-200 dark:hover:bg-red-700 px-4 py-1 rounded-md">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Logsheet Modal -->
    <div id="view-logsheet-modal" tabindex="-1" aria-hidden="true" 
        data-modal-target="view-logsheet-modal"
        data-modal-backdrop="static"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Logsheet Details
                    </h3>
                    <button type="button" 
                        data-modal-hide="view-logsheet-modal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>COA:</strong> <span id="view-coa"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Customer:</strong> <span id="view-customer"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Activity:</strong> <span id="view-activity"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Program:</strong> <span id="view-prodi"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Grade:</strong> <span id="view-grade"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Sequence:</strong> <span id="view-seq"></span>
                            </p>
                        </div>
                        <div>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Quantity (School):</strong> <span id="view-quantity-1"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Rate (School):</strong> <span id="view-rate-1"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Revenue:</strong> <span id="view-revenue"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>AR Status:</strong> <span id="view-ar-status"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Tutor:</strong> <span id="view-tutor"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Quantity (Tutor):</strong> <span id="view-quantity-2"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Rate (Tutor):</strong> <span id="view-rate-2"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>Cost:</strong> <span id="view-cost"></span>
                            </p>
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                <strong>AP Status:</strong> <span id="view-ap-status"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Logsheet Modal -->
    <div id="add-logsheet-modal" tabindex="-1" aria-hidden="true"
        data-modal-target="add-logsheet-modal"
        data-modal-backdrop="static"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                        Add New Logsheet
                    </h3>
                    <button type="button" 
                        data-modal-hide="add-logsheet-modal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form id="logsheetForm" method="POST" action="{{ route('logsheet.store') }}" class="p-4 md:p-5">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="project_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Project</label>
                            <select name="project_id" id="project_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                <option value="">Select project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" data-coa="{{ $project->coa }}" data-customer="{{ $project->customer }}" data-activity="{{ $project->activity }}" data-prodi="{{ $project->prodi }}" data-grade="{{ $project->grade }}" data-rate1="{{ $project->rate_1 }}" data-rate2="{{ $project->rate_2 }}">
                                        {{ $project->coa }} - {{ $project->customer }} ({{ $project->activity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="coa" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">COA</label>
                            <input type="text" name="coa" id="coa" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required readonly>
                        </div>
                        <div>
                            <label for="customer" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Customer</label>
                            <select name="customer" id="customer" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                <option value="">Select customer</option>
                                @foreach(['SMKN 20', 'SMKN 43', 'SMKN 59', 'SMKN 22', 'SMKN 70'] as $customer)
                                    <option value="{{ $customer }}">{{ $customer }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="activity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Activity</label>
                            <input type="text" name="activity" id="activity" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" readonly required>
                        </div>
                        <div>
                            <label for="prodi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Program</label>
                            <input type="text" name="prodi" id="prodi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" readonly required>
                        </div>
                        <div>
                            <label for="grade" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Grade</label>
                            <input type="text" name="grade" id="grade" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" readonly required>
                        </div>
                        <div>
                            <label for="seq" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sequence</label>
                            <input type="number" name="seq" id="seq" min="1" max="100" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="quantity_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity (School)</label>
                            <input type="number" name="quantity_1" id="quantity_1" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="rate_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rate (School)</label>
                            <input type="number" name="rate_1" id="rate_1" min="0" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="revenue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Revenue (Auto-calculated)</label>
                            <input type="number" id="revenue" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                        </div>
                        <div>
                            <label for="ar_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">AR Status</label>
                            <input type="text" name="ar_status" id="ar_status" value="Listing" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                        </div>
                        <div>
                            <label for="tutor" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tutor</label>
                            <select name="tutor" id="tutor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                <option value="">Select tutor</option>
                                @foreach(['andar praskasa', 'danu steven', 'michale sudarsono', 'wit urrohman', 'ageng prasetyo'] as $tutor)
                                    <option value="{{ $tutor }}">{{ $tutor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="quantity_2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity (Tutor)</label>
                            <input type="number" name="quantity_2" id="quantity_2" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="rate_2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rate (Tutor)</label>
                            <input type="number" name="rate_2" id="rate_2" min="0" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="cost" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cost (Auto-calculated)</label>
                            <input type="number" id="cost" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                        </div>
                        <div>
                            <label for="ap_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">AP Status</label>
                            <input type="text" name="ap_status" id="ap_status" value="Listing" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900" data-modal-hide="add-logsheet-modal">
                            Cancel
                        </button>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Logsheet Modal -->
    <div id="edit-logsheet-modal" tabindex="-1" aria-hidden="true"
        data-modal-target="edit-logsheet-modal"
        data-modal-backdrop="static"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit Logsheet
                    </h3>
                    <button type="button"
                        data-modal-hide="edit-logsheet-modal"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form id="editLogsheetForm" method="POST" class="p-4 md:p-5">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="edit_project_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Project</label>
                            <select name="project_id" id="edit_project_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                <option value="">Select project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" data-coa="{{ $project->coa }}" data-customer="{{ $project->customer }}" data-activity="{{ $project->activity }}" data-prodi="{{ $project->prodi }}" data-grade="{{ $project->grade }}" data-rate1="{{ $project->rate_1 }}" data-rate2="{{ $project->rate_2 }}">
                                        {{ $project->coa }} - {{ $project->customer }} ({{ $project->activity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="edit_coa" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">COA</label>
                            <input type="text" name="coa" id="edit_coa" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required readonly>
                        </div>
                        <div>
                            <label for="edit_customer" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Customer</label>
                            <select name="customer" id="edit_customer" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                <option value="">Select customer</option>
                                @foreach(['SMKN 20', 'SMKN 43', 'SMKN 59', 'SMKN 22', 'SMKN 70'] as $customer)
                                    <option value="{{ $customer }}">{{ $customer }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_activity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Activity</label>
                            <input type="text" name="activity" id="edit_activity" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" readonly required>
                        </div>
                        <div>
                            <label for="edit_prodi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Program</label>
                            <input type="text" name="prodi" id="edit_prodi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" readonly required>
                        </div>
                        <div>
                            <label for="edit_grade" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Grade</label>
                            <input type="text" name="grade" id="edit_grade" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" readonly required>
                        </div>
                        <div>
                            <label for="edit_seq" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sequence</label>
                            <input type="number" name="seq" id="edit_seq" min="1" max="100" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="edit_quantity_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity (School)</label>
                            <input type="number" name="quantity_1" id="edit_quantity_1" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="edit_rate_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rate (School)</label>
                            <input type="number" name="rate_1" id="edit_rate_1" min="0" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="edit_revenue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Revenue (Auto-calculated)</label>
                            <input type="number" id="edit_revenue" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                        </div>
                        <div>
                            <label for="edit_ar_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">AR Status</label>
                            <select name="ar_status" id="edit_ar_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                @foreach(['Listing', 'Paid', 'Pending'] as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_tutor" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tutor</label>
                            <select name="tutor" id="edit_tutor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                <option value="">Select tutor</option>
                                @foreach(['andar praskasa', 'danu steven', 'michale sudarsono', 'wit urrohman', 'ageng prasetyo'] as $tutor)
                                    <option value="{{ $tutor }}">{{ $tutor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_quantity_2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity (Tutor)</label>
                            <input type="number" name="quantity_2" id="edit_quantity_2" min="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="edit_rate_2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rate (Tutor)</label>
                            <input type="number" name="rate_2" id="edit_rate_2" min="0" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="edit_cost" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cost (Auto-calculated)</label>
                            <input type="number" id="edit_cost" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                        </div>
                        <div>
                            <label for="edit_ap_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">AP Status</label>
                            <select name="ap_status" id="edit_ap_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                                @foreach(['Listing', 'Paid', 'Pending'] as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900" data-modal-hide="edit-logsheet-modal">
                            Cancel
                        </button>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/logsheet.js') }}"></script>
</body>

</html>