<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kanagata - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('src/output.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="font-poppins">
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
                    <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24">
                        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="FlowBite Logo" />
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Kanagata</span>
                    </a>
                </div>
                <div>
                    <h1 class="self-center text-xl sm:text-2xl font-semibold dark:text-white">Dashboard</h1>
                </div>
                <div class="flex items-center">
                    <!-- Notification Button -->
                    <div class="relative mr-3">
                        <button type="button" id="notification-toggle"
                            class="relative p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200">
                            <span class="sr-only">View notifications</span>
                            <!-- Updated Bell Icon -->
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.193-.538 1.193H5.538c-.538 0-.538-.6-.538-1.193 0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26.944-2.112"/>
                            </svg>
                            <!-- Notification Badge -->
                            <div id="notification-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full hidden animate-pulse">
                                <span id="notification-count" class="text-xs text-white font-bold flex items-center justify-center w-full h-full leading-none">0</span>
                            </div>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="notification-dropdown" class="notification-dropdown hidden absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-200 dark:bg-gray-800 dark:border-gray-600 z-50 transform transition-all duration-200 origin-top-right">
                            <!-- Header -->
                            <div class="p-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-t-xl">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.133 12.632v-1.8a5.406 5.406 0 0 0-4.154-5.262.946.946 0 0 0 .021-.106V3.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C6.867 15.018 5 15.614 5 16.807 5 17.4 5 18 5.538 18h6.462a2 2 0 0 0 4 0h6.462c.538 0 .538-.6.538-1.193 0-1.193-1.867-1.789-1.867-4.175Z"/>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                                    </div>
                                    <button id="mark-all-read" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-200 hover:underline">
                                        Mark all as read
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Notification List with Fixed Height & Scroll -->
                            <div id="notification-list" class="notification-list" style="height: 240px; max-height: 240px; overflow-y: auto; overflow-x: hidden;">
                                <!-- Notifications will be loaded here -->
                                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                    <div class="mb-3">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.193-.538 1.193H5.538c-.538 0-.538-.6-.538-1.193 0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26.944-2.112"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium">Loading notifications...</p>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="p-3 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-b-xl">
                                <button id="view-all-notifications" class="w-full text-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium py-2 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-colors duration-200">
                                    View All Notifications
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center ms-3">
                        <div>
                            <button type="button"
                                class="flex text-sm bg-gray-800 dark:bg-white rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
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
                        class="flex items-center p-2 {{ request()->routeIs('dashboard') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M13.5 2c-.178 0-.356.013-.492.022l-.074.005a1 1 0 0 0-.934.998V11a1 1 0 0 0 1 1h7.975a1 1 0 0 0 .998-.934l.005-.074A7.04 7.04 0 0 0 22 10.5 8.5 8.5 0 0 0 13.5 2Z" />
                            <path
                                d="M11 6.025a1 1 0 0 0-1.065-.998 8.5 8.5 0 1 0 9.038 9.039A1 1 0 0 0 17.975 13H11V6.025Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('insight') }}"
                        class="flex items-center p-2 {{ request()->routeIs('insight') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('insight') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
                        class="flex items-center p-2 {{ request()->routeIs('review.*') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('review.*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036a2.63 2.63 0 0 0 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258a2.63 2.63 0 0 0-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.63 2.63 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.63 2.63 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Review</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('projects.index') }}"
                        class="flex items-center p-2 {{ request()->routeIs('projects.*') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('projects.*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
                        class="flex items-center p-2 {{ request()->routeIs('customer.*') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('customer.*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
                        class="flex items-center p-2 {{ request()->routeIs('tutor.*') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('tutor.*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
                        class="flex items-center p-2 {{ request()->routeIs('invoice.*') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('invoice.*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
                        class="flex items-center p-2 {{ request()->routeIs('ledger.*') ? 'text-white bg-blue-500 hover:bg-blue-600' : 'text-gray-900 hover:bg-gray-100' }} rounded-lg dark:text-white dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 {{ request()->routeIs('ledger.*') ? 'text-white' : 'text-gray-500' }} transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- This Month -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">This Month</h2>
                        <span class="text-gray-500" id="current-month"></span>
                    </div>
                    <div class="space-y-4" id="this-month-data">
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Revenue</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="this-month-revenue">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost Project</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="this-month-cost-project">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Gross Margin</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="this-month-gross-margin">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost Operation</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="this-month-cost-operation">Rp0</span>
                            </div>
                        </div>
                        <div class="pb-2">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Profit/Loss</span>
                                <span class="text-xl font-bold text-gray-800 dark:text-gray-200" id="this-month-profit-loss">Rp0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Summary</h2>
                        <span class="text-gray-500" id="current-year-summary"></span>
                    </div>
                    <div class="space-y-4" id="summary-data">
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Revenue</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="summary-revenue">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost Project</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="summary-cost-project">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Gross Margin</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="summary-gross-margin">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost Operation</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="summary-cost-operation">Rp0</span>
                            </div>
                        </div>
                        <div class="pb-2">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Profit/Loss</span>
                                <span class="text-xl font-bold text-gray-800 dark:text-gray-200" id="summary-profit-loss">Rp0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average -->
                <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Average</h2>
                        <span class="text-gray-500">Monthly</span>
                    </div>
                    <div class="space-y-4" id="average-data">
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Revenue</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="average-revenue">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost Project</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="average-cost-project">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Gross Margin</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="average-gross-margin">Rp0</span>
                            </div>
                        </div>
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Cost Operation</span>
                                <span class="text-xl font-medium text-gray-800 dark:text-gray-200" id="average-cost-operation">Rp0</span>
                            </div>
                        </div>
                        <div class="pb-2">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400 mb-1">Profit/Loss</span>
                                <span class="text-xl font-bold text-gray-800 dark:text-gray-200" id="average-profit-loss">Rp0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-white p-6 rounded-lg shadow dark:bg-gray-800">
                    <div class="flex justify-between">
                        <div>
                            <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2" id="current-year">
                                {{ date('Y') }}
                            </h5>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Revenue this year</p>
                        </div>
                    </div>
                    <div id="revenue-chart"></div>
                    <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                        <div class="flex justify-between items-center pt-5">
                            <div class="flex items-center space-x-4">
                                <button type="button" id="prev-year-revenue" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button type="button" id="next-year-revenue" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow dark:bg-gray-800">
                    <div class="flex justify-between">
                        <div>
                            <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2" id="current-year-profit">
                                {{ date('Y') }}
                            </h5>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Profit this year</p>
                        </div>
                    </div>
                    <div id="profit-chart"></div>
                    <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                        <div class="flex justify-between items-center pt-5">
                            <div class="flex items-center space-x-4">
                                <button type="button" id="prev-year-profit" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button type="button" id="next-year-profit" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Projects</h2>
                    <a href="{{ route('projects.index') }}" class="text-blue-600 hover:underline">View all</a>
                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <div class="overflow-x-scroll">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 min-w-full table-fixed">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                    <th scope="col" class="px-4 py-3 text-center w-16">NO</th>
                                    <!-- Basic Information -->
                                    <th scope="col" class="px-4 py-3 w-32">COA</th>
                                    <th scope="col" class="px-4 py-3 w-48">CUSTOMER</th>
                                    <th scope="col" class="px-4 py-3 w-48">ACTIVITY</th>
                                    <th scope="col" class="px-4 py-3 w-32">PRODI</th>
                                    <th scope="col" class="px-4 py-3 w-32">GRADE</th>
                                    <!-- Revenue Details -->
                                    <th scope="col" class="px-4 py-3 text-center w-32">QTY (REV)</th>
                                    <th scope="col" class="px-4 py-3 text-right w-32">RATE (REV)</th>
                                    <th scope="col" class="px-4 py-3 text-right w-32">GT REV</th>
                                    <!-- Cost Details -->
                                    <th scope="col" class="px-4 py-3 text-center w-32">QTY (COST)</th>
                                    <th scope="col" class="px-4 py-3 text-right w-32">RATE (COST)</th>
                                    <th scope="col" class="px-4 py-3 text-right w-32">GT COST</th>
                                    <th scope="col" class="px-4 py-3 text-right w-32">GT MARGIN</th>
                                    <!-- Action -->
                                    <th scope="col" class="px-4 py-3 w-32">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                                @forelse($latestProjects as $index => $project)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                    <!-- Basic Information -->
                                    <td class="px-4 py-3">{{ $project->coa }}</td>
                                    <td class="px-4 py-3">{{ $project->customer }}</td>
                                    <td class="px-4 py-3">{{ $project->activity }}</td>
                                    <td class="px-4 py-3">{{ $project->prodi }}</td>
                                    <td class="px-4 py-3">{{ $project->grade }}</td>
                                    <!-- Revenue Details -->
                                    <td class="px-4 py-3 text-center">{{ $project->quantity_1 }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($project->rate_1, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($project->gt_rev, 0, ',', '.') }}</td>
                                    <!-- Cost Details -->
                                    <td class="px-4 py-3 text-center">{{ $project->quantity_2 }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($project->rate_2, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($project->gt_cost, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($project->gt_margin, 0, ',', '.') }}</td>
                                    <!-- Action -->
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3">
                                            <a href="{{ route('projects.edit', $project->id) }}" class="font-medium text-blue-600 hover:underline">Edit</a>
                                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                </td>
                            </tr>
                                @empty
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td colspan="14" class="px-4 py-3 text-center">No projects found</td>
                                </tr>
                                @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Customer and Tutor Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Customer Section -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Customers</h2>
                        <a href="{{ route('customer.index') }}" class="text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($latestCustomers as $customer)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <img class="w-12 h-12 rounded-lg object-cover border-2 border-gray-200 dark:border-gray-600" 
                                        src="{{ $customer->image ? asset('storage/' . $customer->image) : asset('img/default-customer.png') }}" 
                                        alt="{{ $customer->name }}"
                                        style="object-fit: contain; padding: 2px;">
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $customer->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->email }}</div>
                                </div>
                            </div>
                            <button onclick="handleCustomerDetails('{{ $customer->id }}')" 
                               data-modal-target="customer-detail-modal" 
                               data-modal-toggle="customer-detail-modal"
                               class="text-sm text-blue-600 bg-blue-100 px-4 py-1 rounded-lg hover:bg-blue-200 transition-colors duration-200">Details</button>
                        </div>
                        @if(!$loop->last)
                            <hr class="border-gray-200 dark:border-gray-700">
                        @endif
                        @empty
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            No customers found
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tutor Section -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tutors</h2>
                        <a href="{{ route('tutor.index') }}" class="text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($latestTutors as $tutor)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <img class="w-12 h-12 rounded-lg object-cover border-2 border-gray-200 dark:border-gray-600" 
                                        src="{{ asset('img/tutor-img/' . ($tutor->photo ?? 'default.png')) }}" 
                                        alt="{{ $tutor->name }}"
                                        style="object-fit: contain; padding: 2px;">
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $tutor->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $tutor->email }}</div>
                                </div>
                            </div>
                            <button onclick="handleTutorSchedule('{{ $tutor->id }}')" 
                               class="text-sm text-blue-600 bg-blue-100 px-4 py-1 rounded-lg hover:bg-blue-200 transition-colors duration-200">Schedule</button>
                        </div>
                        @if(!$loop->last)
                            <hr class="border-gray-200 dark:border-gray-700">
                        @endif
                        @empty
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            No tutors found
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Logsheet and Ledger Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Logsheet -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Logsheet</h2>
                        <a href="{{ route('logsheet.index') }}" class="text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($latestLogsheets as $logsheet)
                        <div class="bg-white p-4 rounded-lg shadow dark:bg-gray-800">
                            <div class="text-gray-600 dark:text-gray-400">{{ $logsheet->created_at->format('F Y') }}</div>
                            <div class="font-bold text-lg mt-1">{{ $logsheet->project->coa }} - {{ $logsheet->project->activity }}</div>
                            <div class="flex gap-2 mt-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $logsheet->ar_status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    Status AR: {{ $logsheet->ar_status }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $logsheet->ap_status === 'Paid' ? 'bg-green-100 text-green-800' : ($logsheet->ap_status === 'Listing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    Status AP: {{ $logsheet->ap_status }}
                                </span>
                            </div>
                            <div class="mt-3 text-gray-600 dark:text-gray-400">
                                <span>Customer: {{ $logsheet->project->customer }}</span>
                                <span class="mx-2">•</span>
                                <span>Tutor: {{ $logsheet->tutor }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white p-4 rounded-lg shadow dark:bg-gray-800 text-center text-gray-500">
                            No logsheets found
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Ledger -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Ledger</h2>
                        <a href="{{ route('ledger.index') }}" class="text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($latestLedgers as $ledger)
                        <div class="bg-white p-4 rounded-lg shadow dark:bg-gray-800">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-lg font-semibold">{{ $ledger->category }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $ledger->budget_coa }}</div>
                                </div>
                                <span class="text-lg font-bold">{{ number_format($ledger->credit, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $ledger->month }}</span>
                                <span class="inline-flex items-center px-3 py-1 text-sm rounded-md {{ $ledger->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($ledger->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white p-4 rounded-lg shadow dark:bg-gray-800 text-center text-gray-500">
                            No ledgers found
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/notifications.js') }}"></script>
</body>

</html>