<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kanagata - Tutors</title>
    <link rel="stylesheet" href="{{ asset('src/output.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/modal-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tutor-calendar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    
    <!-- FullCalendar Scripts -->
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.10/index.global.min.js'></script>
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
                    <a href="" class="flex ms-2 md:me-24">
                        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="FlowBite Logo" />
                        <span
                            class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Kanagata</span>
                    </a>
                </div>
                <div>
                    <h1 class="self-center text-xl sm:text-2xl font-semibold dark:text-white">Tutors</h1>
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
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                        role="menuitem">Sign out</a>
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
                    <a href="../../pages/dashboard/"
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
                    <a href="../../pages/insight/"
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
                    <a href="../../pages/project/"
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
                    <a href="../../pages/customer/"
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
                    <a href="../../pages/tutor/"
                        class="flex items-center p-2 text-white rounded-lg hover:text-gray-900 dark:text-white dark:hover:text-white bg-blue-500 hover:bg-blue-600 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-white transition duration-75 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white"
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
                    <a href="../../pages/logsheet/"
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
                    <a href="../../pages/ledger/"
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
            <div class="grid lg:grid-cols-3 sm:grid-cols-1 gap-4 mb-4">
                @foreach($tutors as $tutor)
                <div class="flex flex-col justify-center items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-sm">
                    <div class="flex flex-col items-center pb-4">
                        <div class="relative mb-4">
                            <img class="w-28 h-28 rounded-full shadow-lg" src="{{ $tutor->photo_url }}" alt="{{ $tutor->name }} image"/>
                            <button type="button" 
                                data-tutor-id="{{ $tutor->id }}"
                                data-modal-target="edit-tutor-modal"
                                data-modal-toggle="edit-tutor-modal"
                                class="edit-tutor absolute bottom-0 right-0 p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        </div>
                        <h5 class="text-2xl font-medium text-gray-900 dark:text-white cursor-pointer mb-1" data-modal-target="tutor-details-{{ $tutor->id }}" data-modal-toggle="tutor-details-{{ $tutor->id }}">{{ $tutor->name }}</h5>
                    </div>

                    <div class="text-center">
                        <p class="text-base text-gray-600 dark:text-gray-400">Born</p>
                        <p class="text-base text-gray-600 dark:text-gray-400 mb-2">{{ $tutor->address }}</p>
                        <p class="text-base text-gray-600 dark:text-gray-400">saya adalah tutor dari kanagata</p>
                    </div>

                    <div class="flex justify-center mt-4">
                        <button type="button" 
                            data-modal-target="schedule-modal" 
                            data-modal-toggle="schedule-modal"
                            data-tutor-id="{{ $tutor->id }}"
                            class="schedule-btn py-2 px-10 text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 hover:bg-blue-200 dark:hover:bg-blue-700 font-medium rounded-lg text-base">
                            Schedule
                        </button>
                    </div>

                    <!-- Tutor Details Modal -->
                    <div id="tutor-details-{{ $tutor->id }}" tabindex="-1" aria-hidden="true" 
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full modal-backdrop"
                        data-modal="tutor-details-{{ $tutor->id }}">
                        <div class="relative p-4 w-full max-w-4xl max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 modal-content">
                                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 modal-header">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Tutor Details
                                    </h3>
                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white modal-button" data-modal-hide="tutor-details-{{ $tutor->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <div class="p-4 md:p-5">
                                    <div class="grid grid-cols-[250px_1fr] gap-6">
                                        <!-- Left Column - Photo -->
                                        <div class="text-center">
                                            <img class="w-48 h-48 mx-auto rounded-full object-cover mb-4 shadow-lg" src="{{ $tutor->photo_url }}" alt="{{ $tutor->name }}">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $tutor->name }}</h3>
                                            @if($tutor->ap_listing_amount > 0)
                                                <p class="text-blue-600 dark:text-blue-400 mt-2">
                                                    AP Listing: Rp {{ number_format($tutor->ap_listing_amount, 0, ',', '.') }}
                                                </p>
                                            @endif
                                        </div>
                                        <!-- Right Column - Details -->
                                        <div class="space-y-4">
                                            <div class="form-field">
                                                <label class="form-label">Phone Number</label>
                                                <p class="text-gray-900 dark:text-white">{{ $tutor->masked_phone }}</p>
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">Email Address</label>
                                                <p class="text-gray-900 dark:text-white">{{ $tutor->masked_email }}</p>
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">Address</label>
                                                <p class="text-gray-900 dark:text-white">{{ $tutor->address }}</p>
                                            </div>
                                            <div class="form-field">
                                                <label class="form-label">Description</label>
                                                <p class="text-gray-900 dark:text-white">{{ $tutor->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Edit Tutor Modal -->
    <div id="edit-tutor-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full modal-backdrop"
        data-modal="edit-tutor-modal">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 modal-content">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 modal-header">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit Tutor
                    </h3>
                    <button type="button" 
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white modal-button" 
                        data-modal-hide="edit-tutor-modal"
                        data-modal-target="edit-tutor-modal"
                        data-modal-toggle="edit-tutor-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="editTutorForm" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="grid grid-cols-[250px_1fr] gap-6">
                        <!-- Left Column - Photo -->
                        <div class="text-center">
                            <img id="preview_photo" src="" alt="Tutor photo" class="w-48 h-48 mx-auto rounded-full object-cover mb-4 shadow-lg">
                            <input type="file" name="photo" id="photo" class="hidden" accept="image/*">
                            <label for="photo" class="cursor-pointer py-2 px-4 text-blue-600 dark:text-blue-100 bg-blue-100 dark:bg-blue-600 hover:bg-blue-200 dark:hover:bg-blue-700 font-medium rounded-lg modal-button inline-block">
                                Change Photo
                            </label>
                        </div>
                        <!-- Right Column - Form Fields -->
                        <div class="space-y-4">
                            <div class="form-field">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="edit_name" class="form-input" required>
                            </div>
                            <div class="form-field">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" id="edit_address" class="form-input" required>
                            </div>
                            <div class="form-field">
                                <label for="birth_year" class="form-label">Birth Year</label>
                                <input type="number" name="birth_year" id="edit_birth_year" min="1950" max="{{ date('Y') }}" class="form-input" required>
                            </div>
                            <div class="form-field">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="edit_description" rows="4" class="form-input" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 modal-button" data-modal-hide="edit-tutor-modal">
                            Cancel
                        </button>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 modal-button">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Calendar Modal -->
    <div id="schedule-modal" tabindex="-1" aria-hidden="true" 
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full modal-backdrop"
        data-modal="schedule-modal">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 modal-content">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 modal-header">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Schedule Calendar
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white modal-button" data-modal-hide="schedule-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div class="mb-4">
                        <label for="logsheet_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Logsheet</label>
                        <select id="logsheet_id" name="logsheet_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white form-input">
                            <option value="">Select logsheet</option>
                        </select>
                    </div>
                    <div id="schedule-calendar" class="bg-white dark:bg-gray-800 rounded-lg p-4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div id="add-schedule-modal" tabindex="-1" aria-hidden="true" 
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full modal-backdrop"
        data-modal="add-schedule-modal">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 modal-content">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 modal-header">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Add Schedule
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white modal-button" data-modal-hide="add-schedule-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="add-schedule-form" class="p-4 md:p-5">
                    <div class="grid gap-4 mb-4">
                        <div>
                            <label for="session_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Session Number</label>
                            <select id="session_number" name="session_number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white form-input" required>
                                <option value="">Select session number</option>
                            </select>
                        </div>
                        <div>
                            <label for="schedule_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                            <input type="date" name="schedule_date" id="schedule_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white form-input" required>
                        </div>
                        <div>
                            <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                            <textarea id="notes" name="notes" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white form-input"></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4 modal-footer">
                        <button type="button" class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 modal-button" data-modal-hide="add-schedule-modal">
                            Cancel
                        </button>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 modal-button">
                            Add Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Schedule Modal -->
    <div id="edit-schedule-modal" tabindex="-1" aria-hidden="true" 
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full modal-backdrop"
        data-modal="edit-schedule-modal">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 modal-content">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 modal-header">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit Schedule
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white modal-button" data-modal-hide="edit-schedule-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form id="edit-schedule-form" class="p-4 md:p-5">
                    <div class="grid gap-4 mb-4">
                        <div>
                            <label for="edit_schedule_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                            <input type="date" name="schedule_date" id="edit_schedule_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white form-input" required>
                        </div>
                        <div>
                            <label for="edit_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <select id="edit_status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white form-input" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                            <textarea id="edit_notes" name="notes" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white form-input"></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4 modal-footer">
                        <button type="button" id="delete-schedule" class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 modal-button">
                            Delete
                        </button>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 modal-button">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Crop Modal -->
    <div id="crop-modal" tabindex="-1" aria-hidden="true" 
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full modal-backdrop"
        data-modal="crop-modal">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 modal-content">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 modal-header">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Crop Image
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white modal-button" data-modal-hide="crop-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div class="img-container">
                        <img id="crop-image" src="" alt="Image to crop" style="max-width: 100%;">
                    </div>
                    <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 modal-button" data-modal-hide="crop-modal">
                            Cancel
                        </button>
                        <button type="button" id="crop-button" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 modal-button">
                            Crop & Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/tutor-schedule.js') }}"></script>
    <script src="{{ asset('js/tutor-edit.js') }}"></script>
</body>

</html>