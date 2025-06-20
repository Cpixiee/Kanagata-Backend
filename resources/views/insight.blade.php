<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kanagata - Insight</title>
    <link rel="stylesheet" href="{{ asset('src/output.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
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
                    <h1 class="self-center text-xl sm:text-2xl font-semibold dark:text-white">Insight</h1>
                </div>
                <div class="flex items-center">
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
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ auth()->user()->email }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    Role: {{ auth()->user()->role }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                            role="menuitem">Sign out</button>
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
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-4">
                <div class="flex items-center rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
                    <div class="w-full bg-gray-50 dark:bg-gray-800 rounded-lg p-4 md:p-6">
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
                </div>
                <div class="flex items-center rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
                    <div class="w-full bg-gray-50 dark:bg-gray-800 rounded-lg p-4 md:p-6">
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
            </div>
        </div>
        <div class="mt-8 grid lg:grid-cols-3 sm:grid-cols-1 gap-6">
            <!-- This Month -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">This Month</h3>
                    <div class="flex items-center space-x-2">
                        <button type="button" id="prev-month" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <span class="text-sm text-gray-600 dark:text-gray-400" id="current-month"></span>
                        <button type="button" id="next-month" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
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
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Summary</h3>
                    <span class="text-sm text-gray-600 dark:text-gray-400" id="current-year-summary"></span>
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
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Average</h3>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Monthly</span>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="{{ asset('js/insight.js') }}"></script>
</body>

</html>