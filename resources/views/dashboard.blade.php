<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kanagata - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('src/output.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
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
                        class="flex items-center p-2 text-white rounded-lg hover:text-gray-900 dark:text-white dark:hover:text-white bg-blue-500 hover:bg-blue-600 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-white transition duration-75 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white"
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
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Insight</span>
                    </a>
                </li>
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
                    <a href="#"
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
                    <a href="#"
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
                    <a href="#"
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
            <div class="grid lg:grid-cols-3 sm:grid-cols-1 gap-4 mb-4 ">
                <div class="flex items-center h-auto rounded-sm bg-gray-50 dark:bg-gray-800">
                    <div class="w-full p-8">
                        <div class="flex flex-1 items-center justify-between">
                            <p class="pe-4 font-semibold text-2xl dark:text-white">This Month</p>
                            <p class="ps-4 font-light text-sm dark:text-white">January</p>
                        </div>
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700 dark:text-white">
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Revenue</p>
                                    <p class="font-semibold">19.000.000</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Cost Project</p>
                                    <p class="font-semibold">4.335.000</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Taxes</p>
                                    <p class="font-semibold">475.000</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Gross Margin</p>
                                    <p class="font-semibold">14.190.000</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Cost Operation</p>
                                    <p class="font-semibold">11.250.000</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between text-red-500">
                                    <p class="font-semibold">Profit / Loss</p>
                                    <p class="font-semibold">-4.707.500</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="flex items-center h-auto rounded-sm bg-gray-50 dark:bg-gray-800">
                    <div class="w-full p-8">
                        <div class="flex flex-1 items-center justify-between dark:text-white">
                            <p class="pe-4 font-semibold text-2xl">Summary</p>
                            <p class="ps-4 font-light text-sm">2025</p>
                        </div>
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700 dark:text-white">
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Revenue</p>
                                    <p class="font-semibold">133.035.000</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Cost Project</p>
                                    <p class="font-semibold">44.315.000</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Taxes</p>
                                    <p class="font-semibold">3.325.875</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Gross Margin</p>
                                    <p class="font-semibold">85.394.125</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Cost Operation</p>
                                    <p class="font-semibold">98.080.089</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between text-red-500">
                                    <p class="font-semibold">Profit / Loss</p>
                                    <p class="font-semibold">-12.685.964</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="flex items-center h-auto rounded-sm bg-gray-50 dark:bg-gray-800">
                    <div class="w-full p-8">
                        <div class="flex flex-1 items-center justify-between dark:text-white">
                            <p class="pe-4 font-semibold text-2xl">Average</p>
                            <p class="ps-4 font-light text-sm">2025</p>
                        </div>
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700 dark:text-white">
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Revenue</p>
                                    <p class="font-semibold">16.629.375</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Cost Project</p>
                                    <p class="font-semibold">5.539.375</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Taxes</p>
                                    <p class="font-semibold">415.734</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Gross Margin</p>
                                    <p class="font-semibold">10.674.266</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between">
                                    <p>Cost Operation</p>
                                    <p class="font-semibold">12.260.011</p>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex justify-between text-red-500">
                                    <p class="font-semibold">Profit / Loss</p>
                                    <p class="font-semibold">-1.585.746</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-4 mb-4">
                <div class="flex items-center rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
                    <div class="w-full bg-gray-50 dark:bg-gray-800 rounded-lg p-4 md:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">2025
                                </h5>
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Revenue this year</p>
                            </div>
                        </div>
                        <div id="revenue-chart"></div>
                        <div
                            class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                            <div class="flex justify-between items-center pt-5">
                                <a href="{{ route('dashboard') }}"
                                    class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
                                    Insight
                                    <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 9 4-4-4-4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
                    <div class="w-full bg-gray-50 dark:bg-gray-800 rounded-lg p-4 md:p-6">
                        <div class="flex justify-between">
                            <div>
                                <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">2025
                                </h5>
                                <p class="text-base font-normal text-gray-500 dark:text-gray-400">Profit
                                    this year</p>
                            </div>
                        </div>
                        <div id="profit-chart"></div>
                        <div
                            class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                            <div class="flex justify-between items-center pt-5">
                                <a href="{{ route('dashboard') }}"
                                    class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
                                    Insight
                                    <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 9 4-4-4-4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-4 mb-4">
                <div class="w-full p-4 bg-gray-50 rounded-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-3xl font-bold leading-none text-gray-900 dark:text-white">Customers</h5>
                        <a href="{{ route('customer.index') }}"
                            class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                            View all
                        </a>
                    </div>
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/cust-img/logo-smkn20.png"
                                            alt="Neil image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            SMKN 20 Jakarta
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Details
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center ">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/cust-img/logo-smkn43.png"
                                            alt="Bonnie image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            SMKN 43 Jakarta
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Details
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/cust-img/logo-smkn59.png"
                                            alt="Michael image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            SMKN 59 Jakarta
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Details
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="w-full p-4 bg-gray-50 rounded-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-3xl font-bold leading-none text-gray-900 dark:text-white">Tutors</h5>
                        <a href="#"
                            class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                            View all
                        </a>
                    </div>
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/tutor-img/default.png"
                                            alt="Neil image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            John Pork
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Schedule
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center ">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/tutor-img/default.png"
                                            alt="Bonnie image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            Hugh Janus
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Schedule
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/tutor-img/default.png"
                                            alt="Michael image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            Claude Debussy
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Schedule
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/tutor-img/default.png"
                                            alt="Michael image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            Mike Hawk
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Schedule
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <img class="w-8 h-8 rounded-full" src="../../img/tutor-img/default.png"
                                            alt="Michael image">
                                    </div>
                                    <div class="flex-1 min-w-0 ms-4">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            Wilma Fingerdo
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            email@windster.com
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        <a href="#"
                                            class="text-sm font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 px-2 py-1 rounded-sm hover:bg-blue-200 dark:hover:bg-blue-700">
                                            Schedule
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-sm mb-4">
                <div class="flex items-center justify-between p-8 border-b border-gray-200">
                    <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">Projects
                    </h5>
                    <a href="{{ route('projects.index') }}"
                        class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                        View all
                    </a>
                </div>
                <div class="relative overflow-x-auto sm:rounded-lg bg-gray-50">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead
                            class="text-xs text-gray-700 text-center uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">
                                    COA
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Customer
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Kegiatan
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    PR
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Grade
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Quantity
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Rate
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    GT Revenue
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Quantity
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Rate
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    GT Cost
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    GT Margin
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Summary AR
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    AR Paid
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    AR OS
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Summary AP
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    AP Paid
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    AP OS
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    To Do
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    AR AP
                                </th>
                                <th scope="col" class="px-6 py-3 sticky right-0 bg-gray-50 dark:bg-gray-700 ">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    1
                                </th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    Apple MacBook Pro 17"
                                </th>
                                <td class="px-6 py-4">
                                    Silver
                                </td>
                                <td class="px-6 py-4">
                                    Laptop
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4">
                                    $2999
                                </td>
                                <td class="px-6 py-4 sticky right-0 bg-white dark:bg-gray-900 flex">
                                    <a href="#"
                                        class="mx-2 font-medium text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 hover:bg-blue-200 dark:hover:bg-blue-700 px-4 py-1 rounded-md">Edit</a>
                                    <a href="#"
                                        class="mx-2 font-medium text-red-600 dark:text-red-200 bg-red-100 dark:bg-red-600 hover:bg-red-200 dark:hover:bg-red-700 px-4 py-1 rounded-md">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 sm:grid-cols-1 gap-4 mb-4">
                <div class="flex rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
                    <div class="w-full bg-gray-50 dark:bg-gray-800 rounded-lg p-4 md:p-6">
                        <div class="flex items-center justify-between">
                            <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-white pb-2">Logsheet
                            </h5>
                            <a href="#"
                                class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                                View all
                            </a>
                        </div>
                        <div class="py-4 mt-4">
                            <ol class="relative border-s border-gray-200 dark:border-gray-700">
                                <li class="mb-10 ms-4 border-b border-gray-200 pb-4">
                                    <div
                                        class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                                    </div>
                                    <time
                                        class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">February
                                        2025</time>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">TA 24/25 - PR.21
                                    </h3>
                                    <div class="">
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-gray-100 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">Customer
                                            :
                                            SMKN
                                            20 Jakarta</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-yellow-100 text-yellow-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">Status
                                            AR : Listing</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Tutor
                                            : Royyan Hidayat</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300">Status
                                            AP : Paid</span>
                                    </div>
                                </li>
                                <li class="mb-10 ms-4 border-b border-gray-200 pb-4">
                                    <div
                                        class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                                    </div>
                                    <time
                                        class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">February
                                        2025</time>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">TA 24/25 - PR.22
                                    </h3>
                                    <div class="">
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-gray-100 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">Customer
                                            :
                                            SMKN
                                            22 Jakarta</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-yellow-100 text-yellow-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">Status
                                            AR : Listing</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Tutor
                                            : Royyan Hidayat</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300">Status
                                            AP : Paid</span>
                                    </div>
                                </li>
                                <li class="mb-10 ms-4 border-b border-gray-200 pb-4">
                                    <div
                                        class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700">
                                    </div>
                                    <time
                                        class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">February
                                        2025</time>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">TA 24/25 - PR.23
                                    </h3>
                                    <div class="">
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-gray-100 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300">Customer
                                            :
                                            SMKN
                                            20 Jakarta</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-yellow-100 text-yellow-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">Status
                                            AR : Listing</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Tutor
                                            : Imaddudin HB</span>
                                        <span
                                            class="whitespace-nowrap inline-block my-2 bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300">Status
                                            AP : Paid</span>
                                    </div>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="flex rounded-sm bg-gray-50 h-auto dark:bg-gray-800">
                    <div class="w-full p-4 rounded-lg md:p-6 sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-3xl font-bold leading-none text-gray-900 dark:text-white">Ledger
                            </h5>
                            <a href="#"
                                class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                                View all
                            </a>
                        </div>
                        <div class="flow-root">
                            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <li class="py-3 sm:py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-medium text-gray-900 truncate dark:text-white">
                                                Rizal Ramadhanu
                                            </p>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">Description
                                                : Cost Operation</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Budget
                                                : Maret 2025</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300">Sub
                                                Budget : By Payroll</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">Status
                                                : Listing</span>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            Rp2.000.000
                                        </div>
                                    </div>
                                </li>
                                <li class="py-3 sm:py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-medium text-gray-900 truncate dark:text-white">
                                                Wirakusuma
                                            </p>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">Description
                                                : Cost Operation</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Budget
                                                : Maret 2025</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300">Sub
                                                Budget : By Payroll</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">Status
                                                : Listing</span>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            Rp1.000.000
                                        </div>
                                    </div>
                                </li>
                                <li class="py-3 sm:py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-medium text-gray-900 truncate dark:text-white">
                                                Dwi Caesario
                                            </p>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">Description
                                                : Cost Operation</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Budget
                                                : Maret 2025</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300">Sub
                                                Budget : By Payroll</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300">Status
                                                : Paid</span>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            Rp0
                                        </div>
                                    </div>
                                </li>
                                <li class="py-3 sm:py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-medium text-gray-900 truncate dark:text-white">
                                                Firly Salsabilla
                                            </p>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">Description
                                                : Cost Operation</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Budget
                                                : Maret 2025</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300">Sub
                                                Budget : By Payroll</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">Status
                                                : Listing</span>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            Rp750.000
                                        </div>
                                    </div>
                                </li>
                                <li class="py-3 sm:py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-medium text-gray-900 truncate dark:text-white">
                                                Rizal Setiadi
                                            </p>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">Description
                                                : Cost Operation</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300">Budget
                                                : Maret 2025</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300">Sub
                                                Budget : By Payroll</span>
                                            <span
                                                class="whitespace-nowrap inline-block my-2 bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-yellow-900 dark:text-yellow-300">Status
                                                : Listing</span>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            Rp1.000.000
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('node_modules/flowbite/dist/flowbite.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="{{ asset('src/chart.js') }}"></script>

</body>

</html>